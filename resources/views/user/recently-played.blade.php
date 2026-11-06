@extends('layouts.app')

@section('title', 'Riwayat Pemutaran - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i> Riwayat Baru Diputar</h2>
            <p class="text-secondary small mb-0">20 trek musik terakhir yang Anda dengarkan di AndosMusic.</p>
        </div>
        <div class="text-secondary small">
            Riwayat tersimpan: <strong class="text-cyan">{{ count($recentTracks) }}</strong> lagu unik
        </div>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul & Artis</th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th class="text-center">Durasi</th>
                        <th>Terakhir Diputar</th>
                        <th class="text-center" style="width: 100px;">Putar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $index => $history)
                        @if($history->music)
                            @php
                                $music = $history->music;
                                $isFav = $music->isFavoritedBy(auth()->user());
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 44px; height: 44px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('music.show', $music->id) }}" class="fw-bold text-white text-decoration-none hover-cyan">
                                                {{ $music->title }}
                                            </a>
                                            <div class="text-secondary small">{{ $music->artist }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary small">{{ $music->album ?: '-' }}</td>
                                <td><span class="genre-tag">{{ $music->genre }}</span></td>
                                <td class="text-center text-muted small">{{ $music->formatted_duration }}</td>
                                <td class="text-muted small">
                                    <i class="fa-regular fa-clock me-1 text-cyan"></i> {{ $history->played_at->diffForHumans() }}
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-cyan btn-sm rounded-circle p-2" 
                                            style="width: 34px; height: 34px;"
                                            data-action="play-track"
                                            data-id="{{ $music->id }}"
                                            data-title="{{ $music->title }}"
                                            data-artist="{{ $music->artist }}"
                                            data-cover="{{ $music->cover_url }}"
                                            data-audio="{{ $music->audio_url }}"
                                            data-duration="{{ $music->duration }}"
                                            data-favorited="{{ $isFav ? 'true' : 'false' }}"
                                            title="Putar Kembali">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-headphones text-muted fs-2 mb-2 d-block"></i>
                                <h6 class="text-white fw-bold">Belum ada riwayat pemutaran</h6>
                                <p class="text-secondary small mb-3">Mulai dengarkan lagu favoritmu hari ini!</p>
                                <a href="{{ route('explore') }}" class="btn btn-cyan btn-sm">Mulai Mendengarkan</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

