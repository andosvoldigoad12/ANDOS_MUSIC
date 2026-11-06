<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Music;
use App\Models\MusicReport;
use App\Models\Comment;
use App\Models\PlayHistory;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalMusic = Music::count();
        $pendingMusic = Music::where('status', 'pending')->count();
        $approvedMusic = Music::where('status', 'approved')->count();
        $rejectedMusic = Music::where('status', 'rejected')->count();
        $totalPlays = Music::sum('plays_count');

        $topMusic = Music::approved()->orderBy('plays_count', 'desc')->take(5)->get();
        $recentActivities = ActivityLog::with('user')->latest()->take(8)->get();
        $recentUploads = Music::with('uploader')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMusic',
            'pendingMusic',
            'approvedMusic',
            'rejectedMusic',
            'totalPlays',
            'topMusic',
            'recentActivities',
            'recentUploads'
        ));
    }

    public function pendingMusic()
    {
        $pendingTracks = Music::with('uploader')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.music.pending', compact('pendingTracks'));
    }

    public function approveMusic($id)
    {
        $music = Music::findOrFail($id);
        $music->status = 'approved';
        $music->rejection_reason = null;
        $music->save();

        // Notify uploader
        Notification::create([
            'user_id' => $music->user_id,
            'title' => 'Musik Anda Telah Disetujui! 🎉',
            'message' => "Karya musik '{$music->title}' telah disetujui oleh moderator dan sekarang dapat didengarkan oleh publik.",
            'type' => 'approval',
            'link' => route('music.show', $music->id),
        ]);

        ActivityLog::log('APPROVE_MUSIC', "Musik '{$music->title}' (#{$music->id}) disetujui oleh " . Auth::user()->name . ".");

        return back()->with('success', "Musik '{$music->title}' berhasil disetujui dan telah dipublikasikan.");
    }

    public function rejectMusic(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $music = Music::findOrFail($id);
        $reason = $request->input('rejection_reason');

        $music->status = 'rejected';
        $music->rejection_reason = $reason;
        $music->save();

        // Notify uploader
        Notification::create([
            'user_id' => $music->user_id,
            'title' => 'Musik Ditolak Moderator ⚠️',
            'message' => "Upload musik '{$music->title}' belum dapat disetujui. Alasan: {$reason}",
            'type' => 'rejection',
            'link' => route('music.my-uploads'),
        ]);

        ActivityLog::log('REJECT_MUSIC', "Musik '{$music->title}' (#{$music->id}) ditolak oleh " . Auth::user()->name . ". Alasan: {$reason}");

        return back()->with('success', "Musik '{$music->title}' telah ditolak dengan alasan yang tercatat.");
    }

    public function allMusic(Request $request)
    {
        $query = Music::with('uploader');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%");
            });
        }

        $musicList = $query->latest()->paginate(15)->withQueryString();

        return view('admin.music.index', compact('musicList'));
    }

    public function manageUsers(Request $request)
    {
        $query = User::where('role', 'user');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->withCount(['music', 'playlists'])->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleUserStatus($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        // Admin cannot touch super admins or other admins
        if ($targetUser->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah status Super Admin.');
        }

        if ($targetUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengubah status Admin.');
        }

        $newStatus = $targetUser->status === 'active' ? 'inactive' : 'active';
        $targetUser->status = $newStatus;
        $targetUser->save();

        ActivityLog::log('TOGGLE_USER_STATUS', "Status akun {$targetUser->name} ({$targetUser->email}) diubah menjadi {$newStatus} oleh {$currentUser->name}.");

        return back()->with('success', "Status akun {$targetUser->name} berhasil diubah menjadi {$newStatus}.");
    }

    public function reports(Request $request)
    {
        $query = MusicReport::with(['user', 'music', 'handler']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function updateReport(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:pending,resolved,dismissed'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = MusicReport::findOrFail($id);
        $report->status = $request->input('status');
        $report->admin_notes = $request->input('admin_notes');
        $report->handled_by = Auth::id();
        $report->save();

        ActivityLog::log('UPDATE_REPORT', "Laporan #{$report->id} diubah statusnya menjadi {$report->status} oleh " . Auth::user()->name . ".");

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function comments(Request $request)
    {
        $query = Comment::with(['user', 'music']);

        if ($search = $request->input('q')) {
            $query->where('content', 'like', "%{$search}%");
        }

        $comments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function statistics()
    {
        // Monthly Plays count (last 6 months)
        $monthlyPlays = PlayHistory::selectRaw('MONTH(played_at) as month, COUNT(*) as count')
            ->where('played_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();

        // Genre distribution
        $genreCounts = Music::approved()
            ->selectRaw('genre, count(*) as count')
            ->groupBy('genre')
            ->pluck('count', 'genre')->toArray();

        $topTracks = Music::approved()->orderBy('plays_count', 'desc')->take(10)->get();

        return view('admin.statistics', compact('monthlyPlays', 'genreCounts', 'topTracks'));
    }
}

