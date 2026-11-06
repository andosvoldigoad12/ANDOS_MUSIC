<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Music;
use App\Models\Favorite;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlaylistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $playlists = Playlist::where('user_id', $user->id)
            ->withCount('music')
            ->latest()
            ->paginate(12);

        return view('playlists.index', compact('playlists'));
    }

    public function create()
    {
        return view('playlists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('playlists', 'public');
        }

        $playlist = Playlist::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'cover_image' => $coverPath,
            'is_public' => $request->boolean('is_public', true),
        ]);

        ActivityLog::log('CREATE_PLAYLIST', "User {$user->name} membuat playlist '{$playlist->name}'.", $user);

        return redirect()->route('playlists.show', $playlist->id)->with('success', 'Playlist baru berhasil dibuat!');
    }

    public function show($id)
    {
        $playlist = Playlist::with(['user', 'music' => function ($q) {
            $q->approved();
        }])->findOrFail($id);

        // Check privacy
        if (!$playlist->is_public && (!Auth::check() || Auth::id() !== $playlist->user_id)) {
            abort(403, 'Playlist ini bersifat pribadi.');
        }

        return view('playlists.show', compact('playlist'));
    }

    public function edit($id)
    {
        $playlist = Playlist::findOrFail($id);
        if (Auth::id() !== $playlist->user_id) {
            abort(403, 'Anda tidak berhak mengedit playlist ini.');
        }

        return view('playlists.edit', compact('playlist'));
    }

    public function update(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        if (Auth::id() !== $playlist->user_id) {
            abort(403, 'Anda tidak berhak mengedit playlist ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($playlist->cover_image && Storage::disk('public')->exists($playlist->cover_image)) {
                Storage::disk('public')->delete($playlist->cover_image);
            }
            $playlist->cover_image = $request->file('cover_image')->store('playlists', 'public');
        }

        $playlist->name = $validated['name'];
        $playlist->description = $validated['description'];
        $playlist->is_public = $request->boolean('is_public');
        $playlist->save();

        ActivityLog::log('UPDATE_PLAYLIST', "Playlist '{$playlist->name}' diperbarui.");

        return redirect()->route('playlists.show', $playlist->id)->with('success', 'Playlist berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $playlist = Playlist::findOrFail($id);
        if (Auth::id() !== $playlist->user_id) {
            abort(403, 'Anda tidak berhak menghapus playlist ini.');
        }

        if ($playlist->cover_image && Storage::disk('public')->exists($playlist->cover_image)) {
            Storage::disk('public')->delete($playlist->cover_image);
        }

        $name = $playlist->name;
        $playlist->delete();

        ActivityLog::log('DELETE_PLAYLIST', "Playlist '{$name}' dihapus.");

        return redirect()->route('playlists.index')->with('success', "Playlist '{$name}' berhasil dihapus.");
    }

    public function addMusic(Request $request, $playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        if (Auth::id() !== $playlist->user_id) {
            abort(403, 'Anda tidak memiliki akses ke playlist ini.');
        }

        $musicId = $request->input('music_id');
        $music = Music::approved()->findOrFail($musicId);

        // Check if already in playlist
        if ($playlist->music()->where('music_id', $music->id)->exists()) {
            return back()->with('error', "Musik '{$music->title}' sudah ada di dalam playlist ini.");
        }

        $order = $playlist->music()->count() + 1;
        $playlist->music()->attach($music->id, ['order' => $order]);

        return back()->with('success', "Musik '{$music->title}' berhasil ditambahkan ke '{$playlist->name}'.");
    }

    public function removeMusic($playlistId, $musicId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        if (Auth::id() !== $playlist->user_id) {
            abort(403, 'Anda tidak memiliki akses ke playlist ini.');
        }

        $playlist->music()->detach($musicId);

        return back()->with('success', 'Musik berhasil dihapus dari playlist.');
    }

    public function favoritesIndex()
    {
        $user = Auth::user();
        $favorites = $user->favoriteMusic()
            ->approved()
            ->latest('favorites.created_at')
            ->paginate(15);

        return view('user.favorites', compact('favorites'));
    }
}

