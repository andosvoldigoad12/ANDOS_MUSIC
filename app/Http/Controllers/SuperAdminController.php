<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Music;
use App\Models\Playlist;
use App\Models\PlayHistory;
use App\Models\ActivityLog;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalSuperAdmins = User::where('role', 'super_admin')->count();
        $totalMusic = Music::count();
        $totalPlaylists = Playlist::count();
        $totalPlays = Music::sum('plays_count');

        $pendingMusic = Music::where('status', 'pending')->count();
        $approvedMusic = Music::where('status', 'approved')->count();
        $rejectedMusic = Music::where('status', 'rejected')->count();

        // Top 5 tracks
        $topMusic = Music::approved()->orderBy('plays_count', 'desc')->take(5)->get();

        // Recent 10 activity logs
        $recentActivities = ActivityLog::with('user')->latest()->take(10)->get();

        // User registration stats for last 7 days
        $userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $userGrowth[$date] = User::whereDate('created_at', $date)->count();
        }

        // Daily play histories for last 7 days
        $dailyPlays = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyPlays[$date] = PlayHistory::whereDate('played_at', $date)->count();
        }

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalSuperAdmins',
            'totalMusic',
            'totalPlaylists',
            'totalPlays',
            'pendingMusic',
            'approvedMusic',
            'rejectedMusic',
            'topMusic',
            'recentActivities',
            'userGrowth',
            'dailyPlays'
        ));
    }

    // Manage Admins
    public function manageAdmins()
    {
        $admins = User::where('role', 'admin')->latest()->paginate(15);
        return view('superadmin.admins.index', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'active',
        ]);

        ActivityLog::log('CREATE_ADMIN', "Super Admin " . Auth::user()->name . " menambahkan Admin baru: {$admin->name} ({$admin->email}).");

        return back()->with('success', "Admin baru '{$admin->name}' berhasil ditambahkan.");
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', Password::min(8)],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }
        $admin->save();

        ActivityLog::log('UPDATE_ADMIN', "Data Admin '{$admin->name}' diperbarui oleh " . Auth::user()->name . ".");

        return back()->with('success', "Data Admin '{$admin->name}' berhasil diperbarui.");
    }

    // Manage Super Admins
    public function manageSuperAdmins()
    {
        $superAdmins = User::where('role', 'super_admin')->latest()->paginate(15);
        return view('superadmin.superadmins.index', compact('superAdmins'));
    }

    public function storeSuperAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        $superAdmin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        ActivityLog::log('CREATE_SUPER_ADMIN', "Super Admin baru ditambahkan: {$superAdmin->name} oleh " . Auth::user()->name . ".");

        return back()->with('success', "Super Admin '{$superAdmin->name}' berhasil ditambahkan.");
    }

    public function toggleSuperAdminStatus($id)
    {
        $target = User::where('role', 'super_admin')->findOrFail($id);
        $current = Auth::user();

        // 1. Protection against self-deactivation
        if ($target->id === $current->id) {
            return back()->with('error', 'Perlindungan Keamanan: Anda tidak dapat menonaktifkan akun Super Admin Anda sendiri.');
        }

        // 2. Protection against deactivating the last active super admin
        if ($target->status === 'active') {
            $activeSuperAdminsCount = User::where('role', 'super_admin')->where('status', 'active')->count();
            if ($activeSuperAdminsCount <= 1) {
                return back()->with('error', 'Perlindungan Keamanan: Tidak dapat menonaktifkan Super Admin aktif terakhir dalam sistem.');
            }
        }

        $newStatus = $target->status === 'active' ? 'inactive' : 'active';
        $target->status = $newStatus;
        $target->save();

        ActivityLog::log('TOGGLE_SUPER_ADMIN_STATUS', "Status Super Admin {$target->name} diubah menjadi {$newStatus} oleh {$current->name}.");

        return back()->with('success', "Status Super Admin '{$target->name}' berhasil diubah menjadi {$newStatus}.");
    }

    // Activity Logs Viewer
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($activity = $request->input('activity')) {
            $query->where('activity', $activity);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        $activities = ActivityLog::select('activity')->distinct()->pluck('activity');

        return view('superadmin.activity-logs', compact('logs', 'activities'));
    }

    // Website Settings
    public function settings()
    {
        $settings = [
            'site_name' => WebsiteSetting::get('site_name', 'AM (AndosMusic)'),
            'tagline' => WebsiteSetting::get('tagline', 'Your Music, Your World.'),
            'contact_email' => WebsiteSetting::get('contact_email', 'support@andosmusic.com'),
            'announcement' => WebsiteSetting::get('announcement', ''),
        ];

        return view('superadmin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'announcement' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($validated as $key => $value) {
            WebsiteSetting::set($key, $value);
        }

        ActivityLog::log('UPDATE_SETTINGS', "Pengaturan website diperbarui oleh " . Auth::user()->name . ".");

        return back()->with('success', 'Pengaturan website berhasil disimpan.');
    }
}

