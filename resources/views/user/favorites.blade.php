@extends('layouts.app')

@section('title', 'Lagu Favorit Saya - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-heart text-danger me-2"></i> Lagu Favorit Saya</h2>
            <p class="text-secondary small mb-0">Daftar seluruh musik yang telah Anda sukai.</p>
        </div>
        <div class="text-secondary small">
            Total: <strong class="text-danger">{{ $favorites->total() }}</strong> lagu
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        @forelse($favorites as $music)
            <div class="col">
                <x-music-card :music="$music" />
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-heart-crack text-muted fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold text-white mb-2">Belum ada lagu favorit</h5>
                <p class="text-secondary small mb-3">Klik ikon hati pada lagu mana pun untuk menambahkannya ke koleksi favorit Anda.</p>
                <a href="{{ route('explore') }}" class="btn btn-cyan btn-sm">Jelajahi Musik</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $favorites->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

