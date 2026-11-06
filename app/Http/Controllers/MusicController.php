<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\PlayHistory;
use App\Models\MusicReport;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Http\Requests\StoreMusicRequest;
use App\Http\Requests\UpdateMusicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MusicController extends Controller
{
    public function create()
    {
        return view('music.create');
    }

    public function store(StoreMusicRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        // 1. Store Audio File
        $audioPath = $request->file('audio_file')->store('music', 'public');

        // 2. Store Cover Image
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        // 3. Determine Initial Status
        // Admin and Super Admin uploads are auto-approved. User uploads are pending moderation.
        $status = $user->isStaff() ? 'approved' : 'pending';

        $music = Music::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album' => $validated['album'] ?? null,
            'genre' => $validated['genre'],
            'description' => $validated['description'] ?? null,
            'cover_image' => $coverPath,
            'audio_file' => $audioPath,
            'duration' => $validated['duration'] ?? 180, // fallback 3 minutes if not extracted
            'status' => $status,
            'plays_count' => 0,
        ]);

        ActivityLog::log(
            'UPLOAD_MUSIC',
            "User {$user->name} mengunggah musik baru '{$music->title}' oleh '{$music->artist}' (Status: {$status}).",
            $user
        );

        if ($status === 'approved') {
            return redirect()->route('music.show', $music->id)->with('success', 'Musik Anda berhasil diunggah dan langsung dipublikasikan!');
        }

        return redirect()->route('music.my-uploads')->with('success', 'Musik Anda berhasil diunggah dan sedang menunggu persetujuan moderator.');
    }

    public function show($id)
    {
        $music = Music::with(['uploader', 'comments.user'])->findOrFail($id);

        // If not approved, only uploader or staff can view
        if ($music->status !== 'approved') {
            if (!Auth::check() || (!Auth::user()->isStaff() && Auth::id() !== $music->user_id)) {
                abort(404, 'Musik ini belum disetujui atau tidak tersedia.');
            }
        }

        // Related tracks (same genre, approved, excluding current)
        $relatedMusic = Music::approved()
            ->where('id', '!=', $music->id)
            ->where('genre', $music->genre)
            ->take(6)
            ->get();

        // User playlists for "Add to playlist" modal
        $userPlaylists = Auth::check() ? Auth::user()->playlists()->latest()->get() : collect();

        // Check if favorited
        $isFavorited = Auth::check() ? $music->isFavoritedBy(Auth::user()) : false;

        return view('music.show', compact('music', 'relatedMusic', 'userPlaylists', 'isFavorited'));
    }

    public function edit($id)
    {
        $music = Music::findOrFail($id);
        $user = Auth::user();

        // Only uploader or staff can edit
        if ($user->id !== $music->user_id && !$user->isStaff()) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit musik ini.');
        }

        return view('music.edit', compact('music'));
    }

    public function update(UpdateMusicRequest $request, $id)
    {
        $music = Music::findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $music->user_id && !$user->isStaff()) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit musik ini.');
        }

        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($music->cover_image && Storage::disk('public')->exists($music->cover_image)) {
                Storage::disk('public')->delete($music->cover_image);
            }
            $music->cover_image = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('audio_file')) {
            if ($music->audio_file && Storage::disk('public')->exists($music->audio_file)) {
                Storage::disk('public')->delete($music->audio_file);
            }
            $music->audio_file = $request->file('audio_file')->store('music', 'public');

            // If a non-staff user updates audio file, put back into pending moderation
            if (!$user->isStaff()) {
                $music->status = 'pending';
            }
        }

        $music->title = $validated['title'];
        $music->artist = $validated['artist'];
        $music->album = $validated['album'] ?? null;
        $music->genre = $validated['genre'];
        $music->description = $validated['description'] ?? null;
        $music->save();

        ActivityLog::log('EDIT_MUSIC', "Musik '{$music->title}' diperbarui oleh {$user->name}.");

        return redirect()->route('music.show', $music->id)->with('success', 'Informasi musik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $music = Music::findOrFail($id);
        $user = Auth::user();

        // Only uploader or staff can delete
        if ($user->id !== $music->user_id && !$user->isStaff()) {
            abort(403, 'Anda tidak berhak menghapus musik ini.');
        }

        // Delete audio and cover files from storage
        if ($music->audio_file && Storage::disk('public')->exists($music->audio_file)) {
            Storage::disk('public')->delete($music->audio_file);
        }
        if ($music->cover_image && Storage::disk('public')->exists($music->cover_image)) {
            Storage::disk('public')->delete($music->cover_image);
        }

        $title = $music->title;
        $music->delete();

        ActivityLog::log('DELETE_MUSIC', "Musik '{$title}' dihapus oleh {$user->name}.");

        if ($user->isStaff() && request()->routeIs('admin*')) {
            return redirect()->route('admin.music.index')->with('success', "Musik '{$title}' berhasil dihapus.");
        }

        return redirect()->route('music.my-uploads')->with('success', "Musik '{$title}' berhasil dihapus.");
    }

    public function myUploads()
    {
        $user = Auth::user();
        $uploads = Music::where('user_id', $user->id)->latest()->paginate(10);

        return view('user.my-uploads', compact('uploads'));
    }

    // Record Play Count (AJAX)
    public function recordPlay(Request $request, $id)
    {
        $music = Music::approved()->findOrFail($id);

        // Increment play count
        $music->increment('plays_count');

        // Record history
        PlayHistory::create([
            'user_id' => Auth::id(),
            'music_id' => $music->id,
            'played_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'plays_count' => $music->plays_count,
        ]);
    }

    // Toggle Favorite (AJAX)
    public function toggleFavorite($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu untuk menyukai musik.',
            ], 401);
        }

        $user = Auth::user();
        $music = Music::approved()->findOrFail($id);

        $favorite = Favorite::where('user_id', $user->id)
            ->where('music_id', $music->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = "Dihapus dari Musik Favorit";
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'music_id' => $music->id,
            ]);
            $isFavorited = true;
            $message = "Ditambahkan ke Musik Favorit";
        }

        $totalFavorites = $music->favorites()->count();

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited,
            'total_favorites' => $totalFavorites,
            'message' => $message,
        ]);
    }

    // Add Comment
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $music = Music::approved()->findOrFail($id);

        Comment::create([
            'user_id' => Auth::id(),
            'music_id' => $music->id,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Komentar Anda berhasil diposting!');
    }

    // Delete Comment
    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();

        if ($comment->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Anda tidak berhak menghapus komentar ini.');
        }

        $comment->delete();
        ActivityLog::log('DELETE_COMMENT', "Komentar #{$commentId} dihapus oleh {$user->name}.");

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    // Submit Music Report
    public function report(Request $request, $id)
    {
        $validated = $request->validate([
            'reason_category' => ['required', 'string', 'in:copyright,broken,inappropriate,wrong_info,other'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $music = Music::findOrFail($id);

        MusicReport::create([
            'user_id' => Auth::id(),
            'music_id' => $music->id,
            'reason_category' => $validated['reason_category'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        ActivityLog::log('REPORT_MUSIC', "User " . Auth::user()->name . " melaporkan musik '{$music->title}'.");

        return back()->with('success', 'Laporan Anda telah dikirimkan ke tim moderator. Terima kasih atas bantuan Anda!');
    }
}
