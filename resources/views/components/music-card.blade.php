@props(['music'])

@php
    $isFavorited = auth()->check() ? $music->isFavoritedBy(auth()->user()) : false;
@endphp

<div class="music-card">
    <div class="music-card-cover">
        <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" loading="lazy">
        <!-- Play Button Overlay -->
        <button type="button" 
                class="music-play-overlay" 
                data-action="play-track"
                data-id="{{ $music->id }}"
                data-title="{{ $music->title }}"
                data-artist="{{ $music->artist }}"
                data-cover="{{ $music->cover_url }}"
                data-audio="{{ $music->audio_url }}"
                data-duration="{{ $music->duration }}"
                data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
                title="Putar Lagu">
            <i class="fa-solid fa-play"></i>
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-start mb-1">
        <a href="{{ route('music.show', $music->id) }}" class="music-card-title text-decoration-none" title="{{ $music->title }}">
            {{ $music->title }}
        </a>
        @auth
            <button class="btn btn-link p-0 text-secondary border-0" 
                    data-action="toggle-like" 
                    data-music-id="{{ $music->id }}" 
                    title="{{ $isFavorited ? 'Hapus dari favorit' : 'Tambah ke favorit' }}">
                <i class="{{ $isFavorited ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }}"></i>
            </button>
        @endauth
    </div>

    <div class="music-card-artist mb-2" title="{{ $music->artist }}">
        {{ $music->artist }}
    </div>

    <div class="music-card-meta">
        <span class="genre-tag">{{ $music->genre }}</span>
        <span><i class="fa-solid fa-headphones me-1"></i> <span class="play-count-{{ $music->id }}">{{ number_format($music->plays_count) }}</span></span>
    </div>
</div>

