<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Music;
use App\Models\Playlist;
use App\Models\Favorite;
use App\Models\PlayHistory;
use App\Models\Comment;
use App\Models\ActivityLog;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Website Settings
        WebsiteSetting::set('site_name', 'AM (AndosMusic)');
        WebsiteSetting::set('tagline', 'Your Music, Your World.');
        WebsiteSetting::set('contact_email', 'support@andosmusic.com');
        WebsiteSetting::set('announcement', 'Selamat datang di AndosMusic! Platform streaming musik & berbagi musik masa depan.');

        // 2. Seed Users
        $superAdminPassword = env('SUPER_ADMIN_PASSWORD', 'Admin123!');
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@andosmusic.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make($superAdminPassword),
                'role' => 'super_admin',
                'status' => 'active',
                'bio' => 'Pemilik dan pengelola sistem utama AndosMusic.',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@andosmusic.com'],
            [
                'name' => 'Content Moderator Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'status' => 'active',
                'bio' => 'Moderator konten dan kurator playlist AndosMusic.',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@andosmusic.com'],
            [
                'name' => 'Dimas Andos',
                'password' => Hash::make('User123!'),
                'role' => 'user',
                'status' => 'active',
                'bio' => 'Music lover, sound engineer & synthesizer enthusiast.',
            ]
        );

        $artist = User::updateOrCreate(
            ['email' => 'artistan@andosmusic.com'],
            [
                'name' => 'Luna Melody',
                'password' => Hash::make('User123!'),
                'role' => 'user',
                'status' => 'active',
                'bio' => 'Indie producer & songwriter from Jakarta.',
            ]
        );

        // Ensure directories exist
        File::ensureDirectoryExists(storage_path('app/public/music'));
        File::ensureDirectoryExists(storage_path('app/public/covers'));
        File::ensureDirectoryExists(storage_path('app/public/playlists'));

        // 3. Helper to generate playable pleasant harmonic WAV audio files
        $createWav = function (string $filename, array $notes, int $durationSec = 8) {
            $path = storage_path('app/public/music/' . $filename);
            if (file_exists($path) && filesize($path) > 1000) {
                return 'music/' . $filename;
            }

            $sampleRate = 22050;
            $numSamples = $sampleRate * $durationSec;
            $data = '';

            $numNotes = count($notes);
            $noteDuration = $numSamples / $numNotes;

            for ($i = 0; $i < $numSamples; $i++) {
                $noteIndex = (int) floor($i / $noteDuration);
                $freq = $notes[$noteIndex] ?? 440.0;
                $t = $i / $sampleRate;

                // Envelope attack and decay for each note
                $noteSample = $i % (int)$noteDuration;
                $env = 1.0;
                if ($noteSample < 500) {
                    $env = $noteSample / 500;
                } elseif ($noteSample > $noteDuration - 1000) {
                    $env = ($noteDuration - $noteSample) / 1000;
                }

                // Polyphonic harmonic synth tone
                $val = sin(2 * M_PI * $freq * $t) * 0.4
                     + sin(2 * M_PI * ($freq * 1.5) * $t) * 0.25
                     + sin(2 * M_PI * ($freq * 2.0) * $t) * 0.15;
                
                $sample = (int) ($val * $env * 32767);
                $sample = max(-32768, min(32767, $sample));
                $data .= pack('v', $sample);
            }

            $dataSize = strlen($data);
            $header = 'RIFF' . pack('V', 36 + $dataSize)
                    . 'WAVEfmt ' . pack('V', 16) . pack('v', 1) . pack('v', 1)
                    . pack('V', $sampleRate) . pack('V', $sampleRate * 2)
                    . pack('v', 2) . pack('v', 16)
                    . 'data' . pack('V', $dataSize);

            file_put_contents($path, $header . $data);
            return 'music/' . $filename;
        };

        // Helper to generate SVG covers
        $createCover = function (string $filename, string $title, string $color1, string $color2) {
            $path = storage_path('app/public/covers/' . $filename);
            if (file_exists($path)) {
                return 'covers/' . $filename;
            }

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">
  <defs>
    <linearGradient id="cGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$color1}"/>
      <stop offset="100%" stop-color="{$color2}"/>
    </linearGradient>
    <radialGradient id="rGlow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="{$color1}" stop-opacity="0.3"/>
      <stop offset="100%" stop-color="#0B1418" stop-opacity="0.95"/>
    </radialGradient>
  </defs>
  <rect width="500" height="500" fill="#0B1418"/>
  <circle cx="250" cy="250" r="220" fill="url(#rGlow)"/>
  <circle cx="250" cy="250" r="170" fill="none" stroke="{$color1}" stroke-width="2" stroke-opacity="0.3"/>
  <circle cx="250" cy="250" r="120" fill="none" stroke="{$color1}" stroke-width="1.5" stroke-opacity="0.4"/>
  <circle cx="250" cy="250" r="60" fill="url(#cGrad)"/>
  <circle cx="250" cy="250" r="18" fill="#0B1418"/>
  <rect x="50" y="380" width="400" height="60" rx="8" fill="#101E24" opacity="0.85"/>
  <text x="250" y="420" text-anchor="middle" fill="#FFFFFF" font-family="system-ui, sans-serif" font-size="20" font-weight="700">{$title}</text>
</svg>
SVG;
            file_put_contents($path, $svg);
            return 'covers/' . $filename;
        };

        // Create audio files & covers
        $track1Audio = $createWav('cyber-echoes.wav', [440.0, 493.88, 523.25, 587.33, 659.25], 10);
        $track1Cover = $createCover('cyber-echoes.svg', 'CYBER ECHOES', '#00D9E8', '#007F8F');

        $track2Audio = $createWav('midnight-city.wav', [329.63, 392.00, 440.00, 493.88], 8);
        $track2Cover = $createCover('midnight-city.svg', 'MIDNIGHT CITY', '#00AFC1', '#16272E');

        $track3Audio = $createWav('neon-horizon.wav', [523.25, 587.33, 659.25, 783.99], 10);
        $track3Cover = $createCover('neon-horizon.svg', 'NEON HORIZON', '#00E5FF', '#00838F');

        $track4Audio = $createWav('acoustic-breeze.wav', [261.63, 329.63, 392.00, 523.25], 9);
        $track4Cover = $createCover('acoustic-breeze.svg', 'ACOUSTIC BREEZE', '#4DD0E1', '#006064');

        $track5Audio = $createWav('smooth-jazz-night.wav', [349.23, 440.00, 523.25, 659.25], 8);
        $track5Cover = $createCover('smooth-jazz-night.svg', 'SMOOTH JAZZ', '#00ACC1', '#004D40');

        $pendingAudio = $createWav('luna-demo.wav', [293.66, 349.23, 440.00, 523.25], 7);
        $pendingCover = $createCover('luna-demo.svg', 'LUNA DEMO TAPE', '#26C6DA', '#00838F');

        // 4. Seed Music Tracks
        $music1 = Music::updateOrCreate(
            ['title' => 'Cyber Echoes'],
            [
                'user_id' => $superAdmin->id,
                'artist' => 'Andos Soundscape',
                'album' => 'Synthetic Dreams',
                'genre' => 'Electronic',
                'description' => 'A captivating synthwave experience with pulsing cyan arpeggios.',
                'cover_image' => $track1Cover,
                'audio_file' => $track1Audio,
                'duration' => 195,
                'status' => 'approved',
                'plays_count' => 1420,
            ]
        );

        $music2 = Music::updateOrCreate(
            ['title' => 'Midnight City Lights'],
            [
                'user_id' => $admin->id,
                'artist' => 'The Neon Orbit',
                'album' => 'After Dark',
                'genre' => 'Pop',
                'description' => 'Upbeat modern pop tune with rich city vibes and rhythmic basslines.',
                'cover_image' => $track2Cover,
                'audio_file' => $track2Audio,
                'duration' => 180,
                'status' => 'approved',
                'plays_count' => 980,
            ]
        );

        $music3 = Music::updateOrCreate(
            ['title' => 'Neon Horizon'],
            [
                'user_id' => $user->id,
                'artist' => 'Dimas Andos',
                'album' => 'First Pulse',
                'genre' => 'EDM',
                'description' => 'High energy future bass drop designed for late-night drives.',
                'cover_image' => $track3Cover,
                'audio_file' => $track3Audio,
                'duration' => 210,
                'status' => 'approved',
                'plays_count' => 1250,
            ]
        );

        $music4 = Music::updateOrCreate(
            ['title' => 'Acoustic Breeze'],
            [
                'user_id' => $artist->id,
                'artist' => 'Luna Melody',
                'album' => 'Whispers in the Wind',
                'genre' => 'Acoustic',
                'description' => 'Soothing acoustic melody with mellow guitar plucks and warm ambient tones.',
                'cover_image' => $track4Cover,
                'audio_file' => $track4Audio,
                'duration' => 165,
                'status' => 'approved',
                'plays_count' => 640,
            ]
        );

        $music5 = Music::updateOrCreate(
            ['title' => 'Smooth Jazz Night'],
            [
                'user_id' => $superAdmin->id,
                'artist' => 'Blue Lounge Collective',
                'album' => 'Velvet Echoes',
                'genre' => 'Jazz',
                'description' => 'Sophisticated jazz harmony with sweet saxophone accents and upright bass.',
                'cover_image' => $track5Cover,
                'audio_file' => $track5Audio,
                'duration' => 240,
                'status' => 'approved',
                'plays_count' => 450,
            ]
        );

        // Pending track for moderation test
        $pendingMusic = Music::updateOrCreate(
            ['title' => 'Starlight Journey (Demo)'],
            [
                'user_id' => $artist->id,
                'artist' => 'Luna Melody',
                'album' => 'Unreleased Gems',
                'genre' => 'Indie',
                'description' => 'Demo track newly uploaded by Luna Melody waiting for moderator review.',
                'cover_image' => $pendingCover,
                'audio_file' => $pendingAudio,
                'duration' => 155,
                'status' => 'pending',
                'plays_count' => 0,
            ]
        );

        // 5. Seed Playlists
        $playlist = Playlist::updateOrCreate(
            ['name' => 'Top Cyan Vibes', 'user_id' => $user->id],
            [
                'description' => 'Koleksi lagu-lagu santai bernuansa cyan favorit sepanjang masa.',
                'is_public' => true,
            ]
        );

        // Attach tracks to playlist
        $playlist->music()->syncWithoutDetaching([
            $music1->id => ['order' => 1],
            $music2->id => ['order' => 2],
            $music3->id => ['order' => 3],
        ]);

        // 6. Seed Favorites
        Favorite::updateOrCreate(['user_id' => $user->id, 'music_id' => $music1->id]);
        Favorite::updateOrCreate(['user_id' => $user->id, 'music_id' => $music3->id]);
        Favorite::updateOrCreate(['user_id' => $superAdmin->id, 'music_id' => $music1->id]);

        // 7. Seed Comments
        Comment::updateOrCreate(
            ['user_id' => $user->id, 'music_id' => $music1->id],
            ['content' => 'Sound design lagu ini luar biasa jernih! Sangat cocok untuk coding santai.']
        );
        Comment::updateOrCreate(
            ['user_id' => $artist->id, 'music_id' => $music1->id],
            ['content' => 'Harmoni synth-nya sangat terinspirasi dari retro wave era 80-an! Suka banget!']
        );

        // 8. Seed Play Histories
        PlayHistory::create([
            'user_id' => $user->id,
            'music_id' => $music1->id,
            'played_at' => now()->subMinutes(15),
            'ip_address' => '127.0.0.1',
        ]);
        PlayHistory::create([
            'user_id' => $user->id,
            'music_id' => $music2->id,
            'played_at' => now()->subHours(2),
            'ip_address' => '127.0.0.1',
        ]);

        // 9. Seed Activity Log
        ActivityLog::create([
            'user_id' => $superAdmin->id,
            'activity' => 'SYSTEM_INIT',
            'description' => 'Inisialisasi sistem AndosMusic berhasil dilakukan.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder Script',
        ]);
    }
}
