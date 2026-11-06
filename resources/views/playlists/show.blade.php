@extends('layouts.app')

@section('title', $playlist->name . ' - Playlist AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <!-- Playlist Header -->
    <div class="p-4 p-md-5 mb-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #13242B 0%, #0B1418 100%); border: 1px solid var(--border-subtle);">
        <div class="row align-items-center g-4">
            <div class="col-md-4 col-lg-3 text-center">
                <img src="{{ $playlist->cover_url }}" alt="{{ $playlist->name }}" class="img-fluid rounded-4 shadow-lg" style="width: 200px; aspect-ratio: 1/1; object-fit: cover; border: 2px solid var(--primary-cyan); box-shadow: 0 10px 30px var(--cyan-glow) !important;">
            </div>
            <div class="col-md-8 col-lg-9">
                <span class="badge bg-cyan text-dark mb-2 px-3 py-1 fw-bold">PLAYLIST</span>
                <h1 class="display-5 fw-extrabold text-white mb-2">{{ $playlist->name }}</h1>
                <p class="text-secondary mb-3">{{ $playlist->description ?: 'Tidak ada deskripsi.' }}</p>

                <div class="d-flex flex-wrap gap-3 text-muted small mb-4">
                    <span><i class="fa-solid fa-user me-1 text-cyan"></i> Dibuat oleh: <strong class="text-white">{{ $playlist->user->name }}</strong></span>
                    <span><i class="fa-solid fa-music me-1 text-cyan"></i> <strong>{{ $playlist->music->count() }}</strong> Lagu</span>
                    <span><i class="fa-solid fa-globe me-1 text-cyan"></i> {{ $playlist->is_public ? 'Publik' : 'Pribadi' }}</span>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($playlist->music->count() > 0)
                        @php
                            $queueJson = $playlist->music->map(function($m) {
                                return [
                                    'id' => $m->id,
                                    'title' => $m->title,
                                    'artist' => $m->artist,
                                    'cover_url' => $m->cover_url,
                                    'audio_url' => $m->audio_url,
                                    'duration' => $m->duration,
                                    'is_favorited' => auth()->check() ? $m->isFavoritedBy(auth()->user()) : false,
                                ];
                            })->values();
                        @endphp
                        <button type="button" class="btn btn-cyan btn-lg px-4" id="playAllBtn">
                            <i class="fa-solid fa-play me-2"></i> Putar Semua
                        </button>
                    @endif

                    @if(auth()->check() && auth()->id() === $playlist->user_id)
                        <a href="{{ route('playlists.edit', $playlist->id) }}" class="btn btn-outline-cyan">
                            <i class="fa-solid fa-pen me-1"></i> Edit Playlist
                        </a>
                        <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST" onsubmit="return confirm('Hapus playlist ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fa-solid fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Song List Table -->
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
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($playlist->music as $index => $music)
                        @php
                            $isFav = auth()->check() ? $music->isFavoritedBy(auth()->user()) : false;
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
                                            title="Putar">
                                        <i class="fa-solid fa-play"></i>
                                    </button>

                                    @if(auth()->check() && auth()->id() === $playlist->user_id)
                                        <form action="{{ route('playlists.remove-music', [$playlist->id, $music->id]) }}" method="POST" onsubmit="return confirm('Keluarkan lagu ini dari playlist?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-1" title="Keluarkan dari playlist">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-music text-muted fs-2 mb-2 d-block"></i>
                                <h6 class="text-white fw-bold">Playlist ini masih kosong</h6>
                                <p class="text-secondary small mb-3">Temukan lagu favorit di menu Explore dan masukkan ke dalam playlist ini.</p>
                                <a href="{{ route('explore') }}" class="btn btn-cyan btn-sm">Jelajahi Musik Sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(isset($queueJson) && $playlist->music->count() > 0)
<script>
    document.getElementById('playAllBtn')?.addEventListener('click', function() {
        const queueData = {!! json_encode($queueJson) !!};
        window.dispatchEvent(new CustomEvent('andos:play', {
            detail: {
                queue: queueData,
                index: 0
            }
        }));
    });
</script>
@endif
@endpush

