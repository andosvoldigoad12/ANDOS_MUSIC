@extends('layouts.app')

@section('title', 'Trending Music - Top 10 AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-fire text-danger me-2"></i> Tangga Lagu Terpopuler (Top 10)</h2>
            <p class="text-secondary small mb-0">Musik yang paling sering didengarkan oleh pengguna AndosMusic secara real-time.</p>
        </div>

        <!-- Period Filter Pills -->
        <div class="btn-group" role="group">
            <a href="{{ route('trending', ['period' => 'today']) }}" class="btn btn-sm {{ $period === 'today' ? 'btn-cyan' : 'btn-outline-cyan' }}">Hari Ini</a>
            <a href="{{ route('trending', ['period' => 'week']) }}" class="btn btn-sm {{ $period === 'week' ? 'btn-cyan' : 'btn-outline-cyan' }}">Minggu Ini</a>
            <a href="{{ route('trending', ['period' => 'month']) }}" class="btn btn-sm {{ $period === 'month' ? 'btn-cyan' : 'btn-outline-cyan' }}">Bulan Ini</a>
            <a href="{{ route('trending', ['period' => 'all']) }}" class="btn btn-sm {{ $period === 'all' ? 'btn-cyan' : 'btn-outline-cyan' }}">Sepanjang Waktu</a>
        </div>
    </div>

    <!-- Top 10 Ranked List -->
    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">#</th>
                        <th>Lagu & Artis</th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-end">Jumlah Putar</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tracks as $index => $music)
                        @php
                            $rank = $index + 1;
                            $isFav = auth()->check() ? $music->isFavoritedBy(auth()->user()) : false;
                        @endphp
                        <tr>
                            <!-- Rank Number -->
                            <td class="text-center fw-bold">
                                @if($rank === 1)
                                    <span class="badge rounded-circle p-2" style="background: #F59E0B; color: #000; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                @elseif($rank === 2)
                                    <span class="badge rounded-circle p-2" style="background: #94A3B8; color: #000; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                @elseif($rank === 3)
                                    <span class="badge rounded-circle p-2" style="background: #00D9E8; color: #000; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                @else
                                    <span class="text-muted">{{ $rank }}</span>
                                @endif
                            </td>

                            <!-- Title & Artist -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2 shadow-sm" style="width: 46px; height: 46px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('music.show', $music->id) }}" class="fw-bold text-white text-decoration-none hover-cyan">
                                            {{ $music->title }}
                                        </a>
                                        <div class="text-secondary small">{{ $music->artist }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Album -->
                            <td class="text-secondary small">{{ $music->album ?: '-' }}</td>

                            <!-- Genre -->
                            <td><span class="genre-tag">{{ $music->genre }}</span></td>

                            <!-- Duration -->
                            <td class="text-center text-muted small">{{ $music->formatted_duration }}</td>

                            <!-- Plays Count -->
                            <td class="text-end fw-bold text-cyan">
                                <i class="fa-solid fa-play text-muted me-1" style="font-size: 0.75rem;"></i>
                                <span class="play-count-{{ $music->id }}">{{ number_format($music->plays_count) }}</span>
                            </td>

                            <!-- Play & Actions -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
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
                                            title="Putar Lagu">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                    @auth
                                        <button type="button" class="btn btn-link text-secondary p-0 border-0" 
                                                data-action="toggle-like" 
                                                data-music-id="{{ $music->id }}" 
                                                title="Suka">
                                            <i class="{{ $isFav ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }}"></i>
                                        </button>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data trending untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

