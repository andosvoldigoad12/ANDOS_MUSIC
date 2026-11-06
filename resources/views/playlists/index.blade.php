@extends('layouts.app')

@section('title', 'Koleksi Playlist Saya - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-list-ul text-cyan me-2"></i> Koleksi Playlist Saya</h2>
            <p class="text-secondary small mb-0">Kelola daftar putar lagu favorit yang telah Anda kurasi.</p>
        </div>
        <a href="{{ route('playlists.create') }}" class="btn btn-cyan btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Buat Playlist Baru
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        @forelse($playlists as $playlist)
            <div class="col">
                <div class="am-card p-3 h-100 d-flex flex-direction-column position-relative hover-card">
                    <a href="{{ route('playlists.show', $playlist->id) }}" class="text-decoration-none">
                        <img src="{{ $playlist->cover_url }}" alt="{{ $playlist->name }}" class="w-100 rounded-3 mb-3 shadow" style="aspect-ratio: 1/1; object-fit: cover;">
                        <h5 class="fw-bold text-white text-truncate mb-1">{{ $playlist->name }}</h5>
                        <p class="text-secondary small text-truncate mb-2">{{ $playlist->description ?: 'Tidak ada deskripsi.' }}</p>
                    </a>
                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: var(--border-color) !important;">
                        <span class="text-muted small"><i class="fa-solid fa-music me-1"></i> {{ $playlist->music_count }} Lagu</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('playlists.edit', $playlist->id) }}" class="btn btn-link text-secondary p-1" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST" onsubmit="return confirm('Hapus playlist ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-1" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-compact-disc text-muted fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold text-white mb-2">Belum ada Playlist</h5>
                <p class="text-secondary small mb-3">Buat playlist pertama Anda dan mulailah mengumpulkan musik-musik favorit.</p>
                <a href="{{ route('playlists.create') }}" class="btn btn-cyan btn-sm">Buat Playlist Pertama</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $playlists->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

