<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AM (AndosMusic) - Your Music, Your World')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom AndosMusic Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/andosmusic.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <a href="{{ url('/') }}" class="brand-logo">
                    <div class="brand-logo-icon">
                        <i class="fa-solid fa-compact-disc"></i>
                    </div>
                    <span>Andos<span style="color: var(--primary-cyan);">Music</span></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Menu Utama</div>
                <a href="{{ route('user.home') }}" class="sidebar-link {{ request()->routeIs('user.home') || request()->is('/') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('explore') }}" class="sidebar-link {{ request()->routeIs('explore*') ? 'active' : '' }}">
                    <i class="fa-solid fa-compass"></i>
                    <span>Explore & Search</span>
                </a>
                <a href="{{ route('trending') }}" class="sidebar-link {{ request()->routeIs('trending*') ? 'active' : '' }}">
                    <i class="fa-solid fa-fire"></i>
                    <span>Trending Music</span>
                </a>

                @auth
                    <div class="nav-section-title">Koleksi Saya</div>
                    <a href="{{ route('playlists.index') }}" class="sidebar-link {{ request()->routeIs('playlists*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>My Playlists</span>
                    </a>
                    <a href="{{ route('favorites.index') }}" class="sidebar-link {{ request()->routeIs('favorites*') ? 'active' : '' }}">
                        <i class="fa-solid fa-heart"></i>
                        <span>Favorites</span>
                    </a>
                    <a href="{{ route('recently-played') }}" class="sidebar-link {{ request()->routeIs('recently-played*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Recently Played</span>
                    </a>

                    <div class="nav-section-title">Studio Musik</div>
                    <a href="{{ route('music.create') }}" class="sidebar-link {{ request()->routeIs('music.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Musik</span>
                    </a>
                    <a href="{{ route('music.my-uploads') }}" class="sidebar-link {{ request()->routeIs('music.my-uploads') ? 'active' : '' }}">
                        <i class="fa-solid fa-music"></i>
                        <span>Upload Saya</span>
                    </a>

                    @if(auth()->user()->isStaff())
                        <div class="nav-section-title">Manajemen Staff</div>
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link text-warning">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Super Admin Panel</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link text-info">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Admin Dashboard</span>
                            </a>
                        @endif
                    @endif
                @else
                    <div class="p-3 mt-3" style="background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <p class="small text-secondary mb-3">Dengarkan jutaan lagu berkualitas tanpa batas di AndosMusic.</p>
                        <a href="{{ route('register') }}" class="btn btn-cyan w-100 mb-2 btn-sm">Daftar Sekarang</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-cyan w-100 btn-sm">Masuk</a>
                    </div>
                @endauth
            </nav>
        </aside>

        <!-- Main Wrapper -->
        <div class="app-main">
            <!-- Topbar -->
            <header class="app-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-icon d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <!-- Global Search Bar -->
                    <form action="{{ route('explore') }}" method="GET" class="topbar-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari lagu, artist, album, genre..." autocomplete="off">
                    </form>
                </div>

                <div class="topbar-actions">
                    <!-- Light / Dark Mode Switch -->
                    <button class="btn-icon" onclick="toggleAndosTheme()" title="Ubah Tema">
                        <i class="fa-solid fa-sun" id="themeToggleIcon"></i>
                    </button>

                    @auth
                        <!-- Notification Dropdown -->
                        <div class="dropdown">
                            <button class="btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                                <i class="fa-solid fa-bell"></i>
                                @php
                                    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2" style="width: 320px; background-color: var(--dropdown-bg); border-color: var(--border-color);">
                                <li class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="border-color: var(--border-color) !important;">
                                    <span class="fw-bold text-primary-cyan small">NOTIFIKASI</span>
                                    @if($unreadCount > 0)
                                        <form action="{{ route('notifications.read-all') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" style="font-size: 0.75rem;">Tandai semua dibaca</button>
                                        </form>
                                    @endif
                                </li>
                                <div style="max-height: 280px; overflow-y: auto;">
                                    @php
                                        $userNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                                    @endphp
                                    @forelse($userNotifications as $notif)
                                        <li>
                                            <a class="dropdown-item p-2 rounded d-flex gap-2 align-items-start {{ !$notif->is_read ? 'bg-card-hover' : '' }}" href="{{ $notif->link ?? '#' }}">
                                                <i class="fa-solid fa-circle-info text-cyan mt-1"></i>
                                                <div style="font-size: 0.82rem;">
                                                    <div class="fw-bold text-white">{{ $notif->title }}</div>
                                                    <div class="text-secondary small">{{ Str::limit($notif->message, 60) }}</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-center py-4 text-muted small">Belum ada notifikasi</li>
                                    @endforelse
                                </div>
                            </ul>
                        </div>

                        <!-- User Profile Dropdown -->
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
                                    <span class="badge bg-cyan text-dark mt-1" style="font-size: 0.68rem;">{{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}</span>
                                </li>
                                <li><a class="dropdown-item py-2 mt-1 rounded text-white" href="{{ route('profile') }}"><i class="fa-regular fa-user me-2 text-secondary"></i> Profil Saya</a></li>
                                <li><a class="dropdown-item py-2 rounded text-white" href="{{ route('music.my-uploads') }}"><i class="fa-solid fa-cloud-arrow-up me-2 text-secondary"></i> Upload Musik Saya</a></li>
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
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-cyan btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-cyan btn-sm">Daftar</a>
                    @endauth
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

            <!-- Page Body -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sticky Persistent Bottom Music Player -->
    <div class="music-player-bar" id="musicPlayerBar">
        <!-- Track Info -->
        <div class="player-track-info">
            <img src="{{ asset('images/default-album.svg') }}" id="playerCover" class="player-cover" alt="Cover">
            <div class="player-details">
                <div class="player-title" id="playerTitle">Pilih lagu untuk memutar</div>
                <div class="player-artist" id="playerArtist">AndosMusic Streaming</div>
            </div>
            @auth
                <button class="btn btn-link text-secondary p-1 ms-2" id="playerLikeBtn" data-action="toggle-like" data-music-id="" title="Suka Musik Ini">
                    <i class="fa-regular fa-heart"></i>
                </button>
            @endauth
        </div>

        <!-- Controls & Progress -->
        <div class="player-controls-container">
            <div class="player-buttons">
                <button class="player-btn" id="playerShuffleBtn" title="Acak Lagu (Shuffle)">
                    <i class="fa-solid fa-shuffle"></i>
                </button>
                <button class="player-btn" id="playerPrevBtn" title="Lagu Sebelumnya">
                    <i class="fa-solid fa-backward-step"></i>
                </button>
                <button class="player-btn player-btn-main" id="playerPlayBtn" title="Play / Pause">
                    <i class="fa-solid fa-play" id="playerPlayIcon"></i>
                </button>
                <button class="player-btn" id="playerNextBtn" title="Lagu Selanjutnya">
                    <i class="fa-solid fa-forward-step"></i>
                </button>
                <button class="player-btn" id="playerRepeatBtn" title="Ulangi Lagu (Repeat)">
                    <i class="fa-solid fa-repeat"></i>
                </button>
            </div>

            <div class="player-progress-area">
                <span class="time-label" id="playerCurrentTime">00:00</span>
                <div class="progress-bar-wrap" id="playerProgressWrap">
                    <div class="progress-bar-fill" id="playerProgressFill"></div>
                </div>
                <span class="time-label" id="playerTotalTime">00:00</span>
            </div>
        </div>

        <!-- Right Side: Volume Controls -->
        <div class="player-actions-right">
            <div class="volume-container">
                <i class="fa-solid fa-volume-high text-secondary" id="playerVolumeIcon" style="cursor: pointer;"></i>
                <input type="range" class="volume-slider" id="playerVolumeSlider" min="0" max="1" step="0.01" value="0.8">
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AndosMusic Audio Player Engine -->
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

