<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
Route::get('/trending', [HomeController::class, 'trending'])->name('trending');
Route::get('/music/{id}', [MusicController::class, 'show'])->name('music.show');
Route::post('/music/{id}/play', [MusicController::class, 'recordPlay'])->name('music.play');

// Guest Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (User, Admin, Super Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/user/home', [HomeController::class, 'userHome'])->name('user.home');

    // Profile & Password
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('password.update');

    // Music Studio (Upload & Manage Own Uploads)
    Route::get('/upload-music', [MusicController::class, 'create'])->name('music.create');
    Route::post('/upload-music', [MusicController::class, 'store'])->name('music.store');
    Route::get('/music/{id}/edit', [MusicController::class, 'edit'])->name('music.edit');
    Route::put('/music/{id}', [MusicController::class, 'update'])->name('music.update');
    Route::delete('/music/{id}', [MusicController::class, 'destroy'])->name('music.destroy');
    Route::get('/my-uploads', [MusicController::class, 'myUploads'])->name('music.my-uploads');

    // Interactions
    Route::post('/music/{id}/favorite', [MusicController::class, 'toggleFavorite'])->name('music.favorite');
    Route::post('/music/{id}/comments', [MusicController::class, 'addComment'])->name('music.comment.store');
    Route::delete('/comments/{id}', [MusicController::class, 'deleteComment'])->name('comments.destroy');
    Route::post('/music/{id}/report', [MusicController::class, 'report'])->name('music.report');

    // Playlists & Favorites
    Route::resource('playlists', PlaylistController::class);
    Route::post('/playlists/{id}/add-music', [PlaylistController::class, 'addMusic'])->name('playlists.add-music');
    Route::delete('/playlists/{id}/music/{musicId}', [PlaylistController::class, 'removeMusic'])->name('playlists.remove-music');
    Route::get('/favorites', [PlaylistController::class, 'favoritesIndex'])->name('favorites.index');
    Route::get('/recently-played', [HomeController::class, 'recentlyPlayed'])->name('recently-played');

    // Notifications
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Admin & Super Admin Shared Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Moderation
    Route::get('/music/pending', [AdminController::class, 'pendingMusic'])->name('music.pending');
    Route::post('/music/{id}/approve', [AdminController::class, 'approveMusic'])->name('music.approve');
    Route::post('/music/{id}/reject', [AdminController::class, 'rejectMusic'])->name('music.reject');

    // Music Management
    Route::get('/music', [AdminController::class, 'allMusic'])->name('music.index');
    Route::get('/music/create', [MusicController::class, 'create'])->name('music.create');

    // Users
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users.index');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');

    // Reports & Comments
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    Route::put('/reports/{id}', [AdminController::class, 'updateReport'])->name('reports.update');
    Route::get('/comments', [AdminController::class, 'comments'])->name('comments.index');

    // Analytics
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
});

/*
|--------------------------------------------------------------------------
| Super Admin Exclusive Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // All Users Management (role change, status toggle)
    Route::get('/users', [SuperAdminController::class, 'allUsers'])->name('users.index');
    Route::put('/users/{id}/role', [SuperAdminController::class, 'updateUserRole'])->name('users.update-role');
    Route::post('/users/{id}/toggle', [SuperAdminController::class, 'toggleAllUserStatus'])->name('users.toggle');

    // Manage Admins
    Route::get('/admins', [SuperAdminController::class, 'manageAdmins'])->name('admins');
    Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{id}', [SuperAdminController::class, 'updateAdmin'])->name('admins.update');

    // Manage Super Admins
    Route::get('/superadmins', [SuperAdminController::class, 'manageSuperAdmins'])->name('superadmins');
    Route::post('/superadmins', [SuperAdminController::class, 'storeSuperAdmin'])->name('superadmins.store');
    Route::post('/superadmins/{id}/toggle', [SuperAdminController::class, 'toggleSuperAdminStatus'])->name('superadmins.toggle');

    // Activity Logs
    Route::get('/activity-logs', [SuperAdminController::class, 'activityLogs'])->name('activity-logs');

    // Website Settings
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
});
