@extends('layouts.app')

@section('title', 'Jelajahi Musik - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-compass text-cyan me-2"></i> Jelajahi Musik</h2>
            <p class="text-secondary small mb-0">Cari lagu favorit, temukan artis baru, dan filter berdasarkan genre musik.</p>
        </div>
        <div class="text-secondary small">
            Ditemukan <strong class="text-cyan">{{ $musicList->total() }}</strong> lagu
        </div>
    </div>

    <!-- Filters & Search Bar Card -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('explore') }}" method="GET" class="row g-3 align-items-center">
            <!-- Search Keyword -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text border-0" style="background-color: var(--bg-input); color: var(--text-muted);"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari judul, artis, album...">
                </div>
            </div>

            <!-- Filter Genre -->
            <div class="col-md-3">
                <select name="genre" class="form-select">
                    <option value="">Semua Genre</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Paling Banyak Diputar</option>
                    <option value="az" {{ request('sort') === 'az' ? 'selected' : '' }}>Judul (A - Z)</option>
                    <option value="za" {{ request('sort') === 'za' ? 'selected' : '' }}>Judul (Z - A)</option>
                </select>
            </div>

            <!-- Submit Button & Reset -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['q', 'genre', 'artist', 'sort']))
                    <a href="{{ route('explore') }}" class="btn btn-outline-secondary" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Music Grid -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        @forelse($musicList as $music)
            <div class="col">
                <x-music-card :music="$music" />
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="brand-logo-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem; opacity: 0.5;">
                    <i class="fa-solid fa-compact-disc"></i>
                </div>
                <h5 class="fw-bold text-white mb-2">Tidak ada musik yang cocok</h5>
                <p class="text-secondary small mb-3">Coba ubah kata kunci pencarian atau reset filter untuk menemukan musik lain.</p>
                <a href="{{ route('explore') }}" class="btn btn-outline-cyan btn-sm">Reset Filter</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $musicList->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

