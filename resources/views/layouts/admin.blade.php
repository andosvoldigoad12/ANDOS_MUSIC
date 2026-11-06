<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AndosMusic - Staff Control Portal')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom AndosMusic Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/andosmusic.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Admin Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('admin.dashboard') }}" class="brand-logo">
                    <div class="brand-logo-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span>AM <span style="color: var(--primary-cyan);">Admin</span></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Overview</div>
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard Super Admin</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard Admin</span>
                    </a>
                @endif

                <div class="nav-section-title">Moderasi & Musik</div>
                @php
                    $pendingCount = \App\Models\Music::where('status', 'pending')->count();
                    $pendingReportCount = \App\Models\MusicReport::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.music.pending') }}" class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.music.pending') ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span>Persetujuan Upload</span>
                    </div>
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.music.index') }}" class="sidebar-link {{ request()->routeIs('admin.music.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-music"></i>
                    <span>Kelola Seluruh Musik</span>
                </a>
                <a href="{{ route('admin.music.create') }}" class="sidebar-link {{ request()->routeIs('admin.music.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload Musik (Auto-Approve)</span>
                </a>

                <div class="nav-section-title">Interaksi & Laporan</div>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Laporan Masalah</span>
                    </div>
                    @if($pendingReportCount > 0)
                        <span class="badge bg-danger">{{ $pendingReportCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->routeIs('admin.comments*') ? 'active' : '' }}">
                    <i class="fa-solid fa-comments"></i>
                    <span>Moderasi Komentar</span>
                </a>

                <div class="nav-section-title">Manajemen Akun</div>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Kelola Pengguna (User)</span>
                </a>

                @if(auth()->user()->isSuperAdmin())
                    <div class="nav-section-title text-warning">Kewenangan Super Admin</div>
                    <a href="{{ route('superadmin.admins') }}" class="sidebar-link {{ request()->routeIs('superadmin.admins*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>Kelola Admin</span>
                    </a>
                    <a href="{{ route('superadmin.superadmins') }}" class="sidebar-link {{ request()->routeIs('superadmin.superadmins*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Kelola Super Admin</span>
                    </a>
                    <a href="{{ route('superadmin.activity-logs') }}" class="sidebar-link {{ request()->routeIs('superadmin.activity-logs*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Activity Logs</span>
                    </a>
                    <a href="{{ route('superadmin.settings') }}" class="sidebar-link {{ request()->routeIs('superadmin.settings*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gear"></i>
                        <span>Pengaturan Website</span>
                    </a>
                @endif

                <div class="nav-section-title">Statistik</div>
                <a href="{{ route('admin.statistics') }}" class="sidebar-link {{ request()->routeIs('admin.statistics*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Statistik & Grafik</span>
                </a>

                <div class="mt-4 p-3">
                    <a href="{{ route('user.home') }}" class="btn btn-outline-cyan w-100 btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Mode Pemutar Lagu
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Wrapper -->
        <div class="app-main" style="margin-bottom: 0;">
            <!-- Topbar -->
            <header class="app-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-icon d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <span class="badge {{ auth()->user()->isSuperAdmin() ? 'bg-warning text-dark' : 'bg-cyan text-dark' }} px-3 py-2 fw-bold">
                        <i class="fa-solid fa-shield-halved me-1"></i>
                        PORTAL {{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>

                <div class="topbar-actions">
                    <!-- Light / Dark Mode Switch -->
                    <button class="btn-icon" onclick="toggleAndosTheme()" title="Ubah Tema">
                        <i class="fa-solid fa-sun" id="themeToggleIcon"></i>
                    </button>

                    <!-- Staff Profile Dropdown -->
                    <div class="dropdown">
                        <button class="user-profile-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                            <span class="fw-semibold text-white small d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down small text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2" style="background-color: var(--dropdown-bg); border-color: var(--border-color);">
                            <li class="px-3 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                                <div class="fw-bold text-white">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item py-2 mt-1 rounded text-white" href="{{ route('profile') }}"><i class="fa-regular fa-user me-2 text-secondary"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item py-2 rounded text-white" href="{{ route('user.home') }}"><i class="fa-solid fa-headphones me-2 text-secondary"></i> Dengar Musik</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 rounded text-danger">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts Banner -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4 mb-0" role="alert" style="background-color: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); color: #10B981;">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4 mb-0" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #EF4444;">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Content Area -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AndosMusic Engine (Player available if previewing audio) -->
    <script src="{{ asset('js/andosmusic-player.js') }}"></script>

    <script>
        // Mobile Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('appSidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>

