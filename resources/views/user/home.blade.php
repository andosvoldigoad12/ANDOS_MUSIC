@extends('layouts.app')

@section('title', 'Beranda - AndosMusic')

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

    <!-- Welcome Hero Section -->
    <div class="p-4 p-md-5 mb-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #13262F 0%, #0B1418 100%); border: 1px solid var(--border-subtle);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-cyan text-dark mb-2 px-3 py-1 fw-bold">
                    <i class="fa-solid fa-music me-1"></i> WELCOME BACK
                </span>
                <h1 class="fw-extrabold text-white mb-2">Halo, <span style="color: var(--primary-cyan);">{{ auth()->user()->name }}</span>! 👋</h1>
                <p class="text-secondary mb-4">Lanjutkan mendengarkan lagu favoritmu atau temukan melodi baru hari ini.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('explore') }}" class="btn btn-cyan btn-sm px-3">
                        <i class="fa-solid fa-compass me-1"></i> Jelajahi Musik
                    </a>
                    <a href="{{ route('music.create') }}" class="btn btn-outline-cyan btn-sm px-3">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Karya Baru
                    </a>
                </div>
            </div>
            <!-- Quick Summary Stats -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <div class="text-secondary small">Koleksi Playlist</div>
                            <div class="fs-4 fw-bold text-cyan">{{ auth()->user()->playlists()->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <div class="text-secondary small">Lagu Favorit</div>
                            <div class="fs-4 fw-bold text-danger">{{ auth()->user()->favorites()->count() }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-secondary small">Musik yang Diunggah</div>
                                    <div class="fs-4 fw-bold text-white">{{ auth()->user()->music()->count() }} Lagu</div>
                                </div>
                                <a href="{{ route('music.my-uploads') }}" class="btn btn-outline-cyan btn-sm py-1">Lihat Status</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Played Preview -->
    @if(count($recentlyPlayed) > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-white mb-0">
                    <i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i> Baru Saja Diputar
                </h4>
                <a href="{{ route('recently-played') }}" class="text-cyan small text-decoration-none fw-semibold">
                    Lihat Riwayat Lengkap <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-4 g-3">
                @foreach($recentlyPlayed as $music)
                    <div class="col">
                        <x-music-card :music="$music" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Genre Chips / Filter -->
    <div class="mb-5">
        <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-tags text-cyan me-2"></i> Genre Populer</h5>
        <div class="d-flex flex-wrap gap-2">
            @foreach($genres as $genre)
                <a href="{{ route('explore', ['genre' => $genre]) }}" class="btn btn-outline-cyan btn-sm rounded-pill px-3 py-2">
                    <i class="fa-solid fa-compact-disc me-1"></i> {{ $genre }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Trending Now -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-white mb-0">
                <i class="fa-solid fa-fire text-danger me-2"></i> Sedang Trending Minggu Ini
            </h4>
            <a href="{{ route('trending') }}" class="text-cyan small text-decoration-none fw-semibold">
                Lihat Top 10 <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($trendingMusic as $music)
                <div class="col">
                    <x-music-card :music="$music" />
                </div>
            @empty
                <div class="col-12 text-muted">Belum ada lagu trending.</div>
            @endforelse
        </div>
    </div>

    <!-- User Playlists Preview -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-white mb-0">
                <i class="fa-solid fa-list-ul text-cyan me-2"></i> Playlist Anda
            </h4>
            <a href="{{ route('playlists.create') }}" class="btn btn-cyan btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Buat Playlist Baru
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3">
            @forelse($myPlaylists as $pl)
                <div class="col">
                    <a href="{{ route('playlists.show', $pl->id) }}" class="text-decoration-none">
                        <div class="am-card p-3 h-100 hover-card">
                            <img src="{{ $pl->cover_url }}" alt="{{ $pl->name }}" class="w-100 rounded-3 mb-2" style="aspect-ratio: 1/1; object-fit: cover;">
                            <div class="fw-bold text-white text-truncate">{{ $pl->name }}</div>
                            <div class="text-secondary small">{{ $pl->music()->count() }} Lagu</div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="p-4 rounded-3 text-center" style="background-color: var(--bg-card); border: 1px dashed var(--border-color);">
                        <p class="text-secondary mb-2">Anda belum memiliki playlist.</p>
                        <a href="{{ route('playlists.create') }}" class="btn btn-cyan btn-sm">Buat Playlist Pertama</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Latest Approved Tracks -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-white mb-0">
                <i class="fa-solid fa-sparkles text-cyan me-2"></i> Baru Saja Ditambahkan
            </h4>
            <a href="{{ route('explore', ['sort' => 'latest']) }}" class="text-cyan small text-decoration-none fw-semibold">
                Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($latestMusic as $music)
                <div class="col">
                    <x-music-card :music="$music" />
                </div>
            @empty
                <div class="col-12 text-muted">Belum ada rilisan musik baru.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

