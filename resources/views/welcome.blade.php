@extends('layouts.app')

@section('title', 'AM (AndosMusic) - Your Music, Your World')

@section('content')
<div class="container-fluid px-0">
    <!-- Announcement Banner -->
    @if(!empty($announcement))
        <div class="alert alert-cyan d-flex align-items-center mb-4 p-3 rounded-3" style="background: rgba(0, 217, 232, 0.1); border: 1px solid rgba(0, 217, 232, 0.25); color: var(--primary-cyan);">
            <i class="fa-solid fa-bullhorn fs-5 me-3"></i>
            <div>
                <span class="fw-bold">Pengumuman:</span> {{ $announcement }}
            </div>
        </div>
    @endif

    <!-- Hero Banner -->
    <div class="p-5 mb-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #10232B 0%, #0B1418 100%); border: 1px solid var(--border-subtle); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <!-- Subtle cyan ambient glow -->
        <div class="position-absolute" style="top: -50px; right: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(0,217,232,0.2) 0%, transparent 70%); filter: blur(40px); pointer-events: none;"></div>

        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <span class="badge bg-cyan text-dark mb-3 px-3 py-2 fw-bold text-uppercase tracking-wider">
                    <i class="fa-solid fa-bolt me-1"></i> Platform Streaming Musik Masa Depan
                </span>
                <h1 class="display-4 fw-extrabold text-white mb-3" style="letter-spacing: -1px;">
                    Your Music, <span style="color: var(--primary-cyan); text-shadow: 0 0 25px var(--cyan-glow);">Your World.</span>
                </h1>
                <p class="lead text-secondary mb-4" style="max-width: 600px;">
                    Dengarkan, simpan, dan bagikan karya musik favoritmu dalam satu sistem terintegrasi. Kualitas audio tinggi, playlist personal, dan ruang untuk setiap musisi.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-cyan btn-lg px-4">
                        <i class="fa-solid fa-play me-2"></i> Mulai Mendengarkan Gratis
                    </a>
                    <a href="{{ route('explore') }}" class="btn btn-outline-cyan btn-lg px-4">
                        <i class="fa-solid fa-compass me-2"></i> Jelajahi Lagu
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="position-relative d-inline-block">
                    <img src="{{ asset('images/default-album.svg') }}" alt="AndosMusic Disc" class="img-fluid rounded-4 shadow-lg" style="width: 260px; transform: rotate(-5deg); border: 2px solid var(--primary-cyan); box-shadow: 0 15px 35px var(--cyan-glow) !important;">
                </div>
            </div>
        </div>
    </div>

    <!-- Trending Music Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-white mb-1">
                    <i class="fa-solid fa-fire text-danger me-2"></i> Sedang Trending
                </h3>
                <p class="text-secondary small mb-0">Lagu-lagu yang paling banyak diputar saat ini di AndosMusic.</p>
            </div>
            <a href="{{ route('trending') }}" class="btn btn-outline-cyan btn-sm">
                Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($trendingMusic as $music)
                <div class="col">
                    <x-music-card :music="$music" />
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-music text-muted fs-1 mb-3"></i>
                    <p class="text-secondary">Belum ada musik trending saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- New Releases Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-white mb-1">
                    <i class="fa-solid fa-sparkles text-cyan me-2"></i> Rilisan Terbaru
                </h3>
                <p class="text-secondary small mb-0">Musik terbaru yang baru saja disetujui dan dirilis oleh kreator.</p>
            </div>
            <a href="{{ route('explore') }}" class="btn btn-outline-cyan btn-sm">
                Jelajahi Semua <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($latestMusic as $music)
                <div class="col">
                    <x-music-card :music="$music" />
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-music text-muted fs-1 mb-3"></i>
                    <p class="text-secondary">Belum ada musik terbaru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Platform Highlights Card -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="am-card p-4 h-100">
                <div class="brand-logo-icon mb-3" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="fa-solid fa-headphones"></i>
                </div>
                <h5 class="fw-bold text-white mb-2">Streaming Audio Berkualitas</h5>
                <p class="text-secondary small mb-0">Putar lagu kesayanganmu dengan lancar tanpa lag melalui engine audio HTML5 modern.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="am-card p-4 h-100">
                <div class="brand-logo-icon mb-3" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <h5 class="fw-bold text-white mb-2">Unggah & Bagikan Musik</h5>
                <p class="text-secondary small mb-0">Kreator dapat mengunggah lagu orisinal, mendapatkan pendengar baru, dan mengelola katalog musik.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="am-card p-4 h-100">
                <div class="brand-logo-icon mb-3" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <h5 class="fw-bold text-white mb-2">Playlist & Riwayat Cerdas</h5>
                <p class="text-secondary small mb-0">Susun koleksi musik impianmu ke dalam playlist kustom dan lacak riwayat pemutaran secara otomatis.</p>
            </div>
        </div>
    </div>
</div>
@endsection
