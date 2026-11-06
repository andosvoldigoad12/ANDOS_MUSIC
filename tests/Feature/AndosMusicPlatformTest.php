<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Music;
use App\Models\Playlist;
use App\Models\Comment;
use App\Models\MusicReport;
use App\Models\PlayHistory;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AndosMusicPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * 1. Public Pages Access
     */
    public function test_public_pages_accessible(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/explore')->assertStatus(200);
        $this->get('/trending')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    /**
     * 2. User Registration forces role USER
     */
    public function test_user_registration_forces_user_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dimas Musisi',
            'email' => 'dimas@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'super_admin', // Attempt tampering
        ]);

        $response->assertRedirect('/user/home');
        $this->assertAuthenticated();

        $user = User::where('email', 'dimas@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role, 'Role must strictly be USER upon public registration!');
        $this->assertEquals('active', $user->status);
        $this->assertTrue(Hash::check('Password123!', $user->password));
    }

    /**
     * 3. Inactive account blocking
     */
    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::create([
            'name' => 'Banned User',
            'email' => 'banned@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'email' => 'banned@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * 4. Strict Role-based Access Control
     */
    public function test_role_based_access_controls(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Content Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // Regular User access checks
        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($user)->get('/superadmin/dashboard')->assertStatus(403);

        // Admin access checks
        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/superadmin/dashboard')->assertStatus(403);

        // Super Admin access checks
        $this->actingAs($superAdmin)->get('/superadmin/dashboard')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/dashboard')->assertStatus(200);
    }

    /**
     * 5. User upload creates PENDING status
     */
    public function test_user_music_upload_creates_pending_status(): void
    {
        $user = User::create([
            'name' => 'Producer User',
            'email' => 'producer@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $fakeAudio = UploadedFile::fake()->create('test-song.mp3', 2048, 'audio/mpeg');
        $fakeCover = UploadedFile::fake()->create('cover.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($user)->post('/upload-music', [
            'title' => 'Starlight Melodies',
            'artist' => 'Producer User',
            'album' => 'Demo Single',
            'genre' => 'Electronic',
            'description' => 'A test song upload.',
            'cover_image' => $fakeCover,
            'audio_file' => $fakeAudio,
            'duration' => 210,
        ]);

        $response->assertRedirect('/my-uploads');

        $music = Music::where('title', 'Starlight Melodies')->first();
        $this->assertNotNull($music);
        $this->assertEquals('pending', $music->status, 'User upload must be PENDING!');
        $this->assertEquals(210, $music->duration);
        Storage::disk('public')->assertExists($music->audio_file);
    }

    /**
     * 6. Staff upload creates APPROVED status
     */
    public function test_staff_music_upload_auto_approves(): void
    {
        $this->withoutExceptionHandling();

        $admin = User::create([
            'name' => 'Admin Curator',
            'email' => 'curator@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $fakeAudio = UploadedFile::fake()->create('curated-song.mp3', 2048, 'audio/mpeg');

        $response = $this->actingAs($admin)->post('/upload-music', [
            'title' => 'Curated Synth Groove',
            'artist' => 'The Curators',
            'genre' => 'EDM',
            'audio_file' => $fakeAudio,
            'duration' => 195,
        ]);

        $music = Music::where('title', 'Curated Synth Groove')->first();
        $this->assertNotNull($music);
        $this->assertEquals('approved', $music->status, 'Staff upload must be AUTO-APPROVED!');
        $response->assertRedirect(route('music.show', $music->id));
    }

    /**
     * 7. Moderation flow: Approve & Reject with reasons
     */
    public function test_moderation_approval_and_rejection_flow(): void
    {
        $user = User::create([
            'name' => 'Indie Musician',
            'email' => 'indie@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Moderator One',
            'email' => 'mod@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create pending track
        $track1 = Music::create([
            'user_id' => $user->id,
            'title' => 'Song To Approve',
            'artist' => 'Indie Musician',
            'genre' => 'Acoustic',
            'audio_file' => 'music/test.wav',
            'status' => 'pending',
            'duration' => 180,
        ]);

        // Create another track for rejection
        $track2 = Music::create([
            'user_id' => $user->id,
            'title' => 'Song To Reject',
            'artist' => 'Indie Musician',
            'genre' => 'Rock',
            'audio_file' => 'music/test2.wav',
            'status' => 'pending',
            'duration' => 180,
        ]);

        // Admin approves track 1
        $this->actingAs($admin)->post("/admin/music/{$track1->id}/approve")->assertSessionHas('success');
        $this->assertEquals('approved', $track1->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'approval',
        ]);

        // Admin rejects track 2 with reason
        $this->actingAs($admin)->post("/admin/music/{$track2->id}/reject", [
            'rejection_reason' => 'Audio clipping and copyrighted backing loop.',
        ])->assertSessionHas('success');

        $this->assertEquals('rejected', $track2->fresh()->status);
        $this->assertEquals('Audio clipping and copyrighted backing loop.', $track2->fresh()->rejection_reason);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'rejection',
        ]);
    }

    /**
     * 8. Play Count increment and PlayHistory
     */
    public function test_music_playback_and_play_count_increment(): void
    {
        $user = User::create([
            'name' => 'Listener',
            'email' => 'listener@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $music = Music::create([
            'user_id' => $user->id,
            'title' => 'Hit Track',
            'artist' => 'Famous Artist',
            'genre' => 'Pop',
            'audio_file' => 'music/hit.mp3',
            'status' => 'approved',
            'plays_count' => 10,
            'duration' => 200,
        ]);

        $response = $this->actingAs($user)->postJson("/music/{$music->id}/play");
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'plays_count' => 11,
        ]);

        $this->assertEquals(11, $music->fresh()->plays_count);
        $this->assertDatabaseHas('play_histories', [
            'user_id' => $user->id,
            'music_id' => $music->id,
        ]);
    }

    /**
     * 9. Favorite Toggle
     */
    public function test_favorite_toggle(): void
    {
        $user = User::create([
            'name' => 'Fan User',
            'email' => 'fan@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $music = Music::create([
            'user_id' => $user->id,
            'title' => 'Favorite Tune',
            'artist' => 'The Stars',
            'genre' => 'Jazz',
            'audio_file' => 'music/jazz.mp3',
            'status' => 'approved',
            'duration' => 210,
        ]);

        // 1. Like
        $resLike = $this->actingAs($user)->postJson("/music/{$music->id}/favorite");
        $resLike->assertJson(['is_favorited' => true, 'total_favorites' => 1]);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'music_id' => $music->id]);

        // 2. Unlike
        $resUnlike = $this->actingAs($user)->postJson("/music/{$music->id}/favorite");
        $resUnlike->assertJson(['is_favorited' => false, 'total_favorites' => 0]);
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'music_id' => $music->id]);
    }

    /**
     * 10. Playlist Management
     */
    public function test_playlist_management(): void
    {
        $user = User::create([
            'name' => 'Playlist Creator',
            'email' => 'playlist@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $music = Music::create([
            'user_id' => $user->id,
            'title' => 'Playlist Song',
            'artist' => 'Artist X',
            'genre' => 'Lo-Fi',
            'audio_file' => 'music/lofi.mp3',
            'status' => 'approved',
            'duration' => 150,
        ]);

        // 1. Create Playlist
        $res = $this->actingAs($user)->post('/playlists', [
            'name' => 'Late Night Vibes',
            'description' => 'Chill beats to relax to.',
            'is_public' => 1,
        ]);

        $playlist = Playlist::where('name', 'Late Night Vibes')->first();
        $this->assertNotNull($playlist);
        $res->assertRedirect(route('playlists.show', $playlist->id));

        // 2. Add Music to Playlist
        $this->actingAs($user)->post("/playlists/{$playlist->id}/add-music", [
            'music_id' => $music->id,
        ])->assertSessionHas('success');

        $this->assertTrue($playlist->fresh()->music->contains($music->id));

        // 3. Remove Music from Playlist
        $this->actingAs($user)->delete("/playlists/{$playlist->id}/music/{$music->id}")
            ->assertSessionHas('success');
        $this->assertFalse($playlist->fresh()->music->contains($music->id));

        // 4. Delete Playlist
        $this->actingAs($user)->delete("/playlists/{$playlist->id}")
            ->assertRedirect(route('playlists.index'));
        $this->assertDatabaseMissing('playlists', ['id' => $playlist->id]);
    }

    /**
     * 11. Comments and Reports
     */
    public function test_comments_and_reports(): void
    {
        $user = User::create([
            'name' => 'Commenter',
            'email' => 'commenter@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $music = Music::create([
            'user_id' => $user->id,
            'title' => 'Discussed Song',
            'artist' => 'Artist A',
            'genre' => 'Pop',
            'audio_file' => 'music/pop.mp3',
            'status' => 'approved',
            'duration' => 180,
        ]);

        // 1. Post Comment
        $this->actingAs($user)->post("/music/{$music->id}/comments", [
            'content' => 'Lagu ini aransemen musiknya mantap sekali!',
        ])->assertSessionHas('success');

        $comment = Comment::where('content', 'Lagu ini aransemen musiknya mantap sekali!')->first();
        $this->assertNotNull($comment);

        // 2. Admin deletes comment
        $this->actingAs($admin)->delete("/comments/{$comment->id}")
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);

        // 3. Submit Music Report
        $this->actingAs($user)->post("/music/{$music->id}/report", [
            'reason_category' => 'broken',
            'description' => 'Audio file stops playing at second 45.',
        ])->assertSessionHas('success');

        $report = MusicReport::where('music_id', $music->id)->first();
        $this->assertNotNull($report);
        $this->assertEquals('pending', $report->status);

        // 4. Admin updates report
        $this->actingAs($admin)->put("/admin/reports/{$report->id}", [
            'status' => 'resolved',
            'admin_notes' => 'Checked and fixed audio file.',
        ])->assertSessionHas('success');

        $this->assertEquals('resolved', $report->fresh()->status);
    }

    /**
     * 12. Super Admin Safeguards
     */
    public function test_super_admin_safeguards(): void
    {
        $superAdmin1 = User::create([
            'name' => 'Super Admin 1',
            'email' => 'sa1@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 1. Super admin cannot deactivate themselves
        $resSelf = $this->actingAs($superAdmin1)->post("/superadmin/superadmins/{$superAdmin1->id}/toggle");
        $resSelf->assertSessionHas('error');
        $this->assertEquals('active', $superAdmin1->fresh()->status);

        // 2. Super admin cannot deactivate the last active super admin
        $superAdmin2 = User::create([
            'name' => 'Super Admin 2',
            'email' => 'sa2@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'super_admin',
            'status' => 'inactive',
        ]);

        // Attempting to deactivate superAdmin1 by another superAdmin (if superAdmin1 is the only active one)
        // With superAdmin2 inactive, superAdmin1 is the ONLY active super admin in the system!
        $superAdmin2->status = 'active';
        $superAdmin2->save();

        // Now with two active super admins, sa1 can toggle sa2
        $this->actingAs($superAdmin1)->post("/superadmin/superadmins/{$superAdmin2->id}/toggle")
            ->assertSessionHas('success');
        $this->assertEquals('inactive', $superAdmin2->fresh()->status);

        // Now superAdmin1 is the ONLY active super admin. If someone tries to deactivate superAdmin1:
        // Even if attempted, it is blocked!
        $this->actingAs($superAdmin1)->post("/superadmin/superadmins/{$superAdmin1->id}/toggle")
            ->assertSessionHas('error');
        $this->assertEquals('active', $superAdmin1->fresh()->status);
    }
}
