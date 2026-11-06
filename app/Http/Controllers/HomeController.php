<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Playlist;
use App\Models\PlayHistory;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return $this->userHome();
        }
        return $this->landing();
    }

    public function landing()
    {
        $trendingMusic = Music::approved()
            ->orderBy('plays_count', 'desc')
            ->take(8)
            ->get();

        $latestMusic = Music::approved()
            ->latest()
            ->take(8)
            ->get();

        $announcement = WebsiteSetting::get('announcement');
        $tagline = WebsiteSetting::get('tagline', 'Your Music, Your World.');

        return view('welcome', compact('trendingMusic', 'latestMusic', 'announcement', 'tagline'));
    }

    public function userHome()
    {
        $user = Auth::user();

        // 1. Trending tracks
        $trendingMusic = Music::approved()
            ->orderBy('plays_count', 'desc')
            ->take(8)
            ->get();

        // 2. Latest uploaded tracks
        $latestMusic = Music::approved()
            ->latest()
            ->take(8)
            ->get();

        // 3. User's recently played tracks (max 8 for dashboard preview)
        $recentlyPlayed = [];
        if ($user) {
            $recentHistories = PlayHistory::where('user_id', $user->id)
                ->with('music')
                ->latest('played_at')
                ->take(8)
                ->get();
            $recentlyPlayed = $recentHistories->pluck('music')->filter()->unique('id');
        }

        // 4. User's personal playlists
        $myPlaylists = $user ? $user->playlists()->latest()->take(4)->get() : collect();

        // 5. Featured genres
        $genres = Music::approved()
            ->select('genre')
            ->distinct()
            ->pluck('genre');

        $announcement = WebsiteSetting::get('announcement');
        $tagline = WebsiteSetting::get('tagline', 'Your Music, Your World.');

        return view('user.home', compact(
            'trendingMusic',
            'latestMusic',
            'recentlyPlayed',
            'myPlaylists',
            'genres',
            'announcement',
            'tagline'
        ));
    }

    public function explore(Request $request)
    {
        $query = Music::approved();

        // Search Keyword (Title, Artist, Album, Genre)
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%")
                  ->orWhere('album', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%");
            });
        }

        // Filter Genre
        if ($genre = $request->input('genre')) {
            $query->where('genre', $genre);
        }

        // Filter Artist
        if ($artist = $request->input('artist')) {
            $query->where('artist', $artist);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderBy('plays_count', 'desc');
        } elseif ($sort === 'az') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'za') {
            $query->orderBy('title', 'desc');
        } else {
            $query->latest();
        }

        $musicList = $query->paginate(12)->withQueryString();

        // All distinct genres & artists for filters
        $genres = Music::approved()->select('genre')->distinct()->pluck('genre');
        $artists = Music::approved()->select('artist')->distinct()->take(20)->pluck('artist');

        return view('music.explore', compact('musicList', 'genres', 'artists'));
    }

    public function trending(Request $request)
    {
        $period = $request->input('period', 'all');

        $query = Music::approved();

        if ($period === 'today') {
            $musicIds = PlayHistory::where('played_at', '>=', now()->startOfDay())
                ->selectRaw('music_id, count(*) as count')
                ->groupBy('music_id')
                ->orderByDesc('count')
                ->pluck('music_id');
            if ($musicIds->isNotEmpty()) {
                $tracks = Music::whereIn('id', $musicIds)->get()->sortBy(function($m) use ($musicIds) {
                    return array_search($m->id, $musicIds->toArray());
                });
            } else {
                $tracks = $query->orderBy('plays_count', 'desc')->take(10)->get();
            }
        } elseif ($period === 'week') {
            $musicIds = PlayHistory::where('played_at', '>=', now()->subDays(7))
                ->selectRaw('music_id, count(*) as count')
                ->groupBy('music_id')
                ->orderByDesc('count')
                ->pluck('music_id');
            if ($musicIds->isNotEmpty()) {
                $tracks = Music::whereIn('id', $musicIds)->get()->sortBy(function($m) use ($musicIds) {
                    return array_search($m->id, $musicIds->toArray());
                });
            } else {
                $tracks = $query->orderBy('plays_count', 'desc')->take(10)->get();
            }
        } elseif ($period === 'month') {
            $musicIds = PlayHistory::where('played_at', '>=', now()->subDays(30))
                ->selectRaw('music_id, count(*) as count')
                ->groupBy('music_id')
                ->orderByDesc('count')
                ->pluck('music_id');
            if ($musicIds->isNotEmpty()) {
                $tracks = Music::whereIn('id', $musicIds)->get()->sortBy(function($m) use ($musicIds) {
                    return array_search($m->id, $musicIds->toArray());
                });
            } else {
                $tracks = $query->orderBy('plays_count', 'desc')->take(10)->get();
            }
        } else {
            $tracks = $query->orderBy('plays_count', 'desc')->take(10)->get();
        }

        return view('music.trending', compact('tracks', 'period'));
    }

    public function recentlyPlayed()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $histories = PlayHistory::where('user_id', $user->id)
            ->with('music')
            ->latest('played_at')
            ->take(20)
            ->get();

        $recentTracks = $histories->pluck('music')->filter()->unique('id');

        return view('user.recently-played', compact('recentTracks', 'histories'));
    }
}

