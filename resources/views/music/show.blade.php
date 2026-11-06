@extends('layouts.app')

@section('title', $music->title . ' - ' . $music->artist . ' | AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <!-- Music Header Hero -->
    <div class="p-4 p-md-5 mb-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #16272E 0%, #0B1418 100%); border: 1px solid var(--border-subtle);">
        <div class="row align-items-center g-4">
            <!-- Cover Art -->
            <div class="col-md-4 col-lg-3 text-center">
                <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="img-fluid rounded-4 shadow-lg" style="width: 220px; aspect-ratio: 1/1; object-fit: cover; border: 2px solid var(--primary-cyan); box-shadow: 0 10px 30px var(--cyan-glow) !important;">
            </div>

            <!-- Metadata & Controls -->
            <div class="col-md-8 col-lg-9">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="genre-tag">{{ $music->genre }}</span>
                    @if($music->status !== 'approved')
                        <span class="badge-status badge-{{ $music->status }}">
                            <i class="fa-solid fa-circle-exclamation"></i> Status: {{ strtoupper($music->status) }}
                        </span>
                    @endif
                </div>

                <h1 class="display-5 fw-extrabold text-white mb-1">{{ $music->title }}</h1>
                <h4 class="text-secondary mb-3">{{ $music->artist }}</h4>

                <div class="d-flex flex-wrap gap-3 text-muted small mb-4">
                    <span><i class="fa-solid fa-compact-disc me-1 text-cyan"></i> Album: <strong>{{ $music->album ?: 'Single' }}</strong></span>
                    <span><i class="fa-solid fa-clock me-1 text-cyan"></i> Durasi: <strong>{{ $music->formatted_duration }}</strong></span>
                    <span><i class="fa-solid fa-headphones me-1 text-cyan"></i> Diputar: <strong class="play-count-{{ $music->id }}">{{ number_format($music->plays_count) }}</strong> kali</span>
                    <span><i class="fa-solid fa-user me-1 text-cyan"></i> Uploader: <strong>{{ $music->uploader->name }}</strong></span>
                    <span><i class="fa-regular fa-calendar me-1 text-cyan"></i> {{ $music->created_at->format('d M Y') }}</span>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <button type="button" 
                            class="btn btn-cyan btn-lg px-4"
                            data-action="play-track"
                            data-id="{{ $music->id }}"
                            data-title="{{ $music->title }}"
                            data-artist="{{ $music->artist }}"
                            data-cover="{{ $music->cover_url }}"
                            data-audio="{{ $music->audio_url }}"
                            data-duration="{{ $music->duration }}"
                            data-favorited="{{ $isFavorited ? 'true' : 'false' }}">
                        <i class="fa-solid fa-play me-2"></i> Putar Lagu
                    </button>

                    @auth
                        <button type="button" 
                                class="btn btn-outline-cyan btn-lg px-3"
                                data-action="toggle-like"
                                data-music-id="{{ $music->id }}">
                            <i class="{{ $isFavorited ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }} me-1"></i>
                            <span class="like-count-{{ $music->id }}">{{ $music->favorites()->count() }}</span> Suka
                        </button>

                        <button type="button" class="btn btn-outline-cyan btn-lg px-3" data-bs-toggle="modal" data-bs-target="#addToPlaylistModal">
                            <i class="fa-solid fa-plus me-1"></i> Masukkan ke Playlist
                        </button>

                        <button type="button" class="btn btn-link text-secondary text-decoration-none small" data-bs-toggle="modal" data-bs-target="#reportModal">
                            <i class="fa-solid fa-flag me-1"></i> Laporkan
                        </button>

                        @if(auth()->user()->isStaff() || auth()->id() === $music->user_id)
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('music.edit', $music->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <form action="{{ route('music.destroy', $music->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus musik ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Alert if rejected -->
    @if($music->status === 'rejected' && $music->rejection_reason)
        <div class="alert alert-danger mb-4 p-4 rounded-3" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #EF4444;">
            <h6 class="fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Alasan Penolakan Moderator:</h6>
            <p class="mb-0">{{ $music->rejection_reason }}</p>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Description & Comments -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="am-card p-4 mb-4">
                <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-circle-info text-cyan me-2"></i> Tentang Lagu Ini</h5>
                <p class="text-secondary mb-0" style="white-space: pre-line;">
                    {{ $music->description ?: 'Belum ada deskripsi untuk lagu ini.' }}
                </p>
            </div>

            <!-- Comments Section -->
            <div class="am-card p-4 mb-4">
                <h5 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-comments text-cyan me-2"></i> Komentar ({{ $music->comments->count() }})
                </h5>

                @auth
                    <!-- Add Comment Form -->
                    <form action="{{ route('music.comment.store', $music->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="d-flex gap-3 align-items-start">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <textarea name="content" rows="2" class="form-control mb-2" required placeholder="Tulis komentar atau apresiasi untuk musisi ini..."></textarea>
                                <button type="submit" class="btn btn-cyan btn-sm">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Kirim Komentar
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="p-3 mb-4 rounded-3 text-center" style="background-color: var(--bg-card-hover); border: 1px dashed var(--border-color);">
                        <p class="text-secondary small mb-2">Ingin ikut berkomentar?</p>
                        <a href="{{ route('login') }}" class="btn btn-cyan btn-sm">Masuk untuk Berkomentar</a>
                    </div>
                @endauth

                <!-- Comments List -->
                <div class="comments-list">
                    @forelse($music->comments as $comment)
                        <div class="d-flex gap-3 py-3 border-top" style="border-color: var(--border-color) !important;">
                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold text-white small">
                                        {{ $comment->user->name }}
                                        @if($comment->user->isStaff())
                                            <span class="badge bg-cyan text-dark ms-1" style="font-size: 0.65rem;">STAFF</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isStaff()))
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Hapus komentar">
                                                    <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-secondary small mb-0">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada komentar. Jadilah orang pertama yang berkomentar!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Related Music -->
        <div class="col-lg-4">
            <div class="am-card p-4">
                <h5 class="fw-bold text-white mb-3">
                    <i class="fa-solid fa-record-vinyl text-cyan me-2"></i> Rekomendasi Sejenis
                </h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedMusic as $rel)
                        @php
                            $relFav = auth()->check() ? $rel->isFavoritedBy(auth()->user()) : false;
                        @endphp
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3 hover-card" style="background-color: var(--bg-card-hover);">
                            <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover;">
                            <div class="flex-grow-1 overflow-hidden">
                                <a href="{{ route('music.show', $rel->id) }}" class="fw-bold text-white text-truncate d-block text-decoration-none small">
                                    {{ $rel->title }}
                                </a>
                                <div class="text-secondary small text-truncate" style="font-size: 0.75rem;">{{ $rel->artist }}</div>
                            </div>
                            <button type="button" 
                                    class="btn btn-cyan btn-sm rounded-circle p-2" 
                                    style="width: 32px; height: 32px;"
                                    data-action="play-track"
                                    data-id="{{ $rel->id }}"
                                    data-title="{{ $rel->title }}"
                                    data-artist="{{ $rel->artist }}"
                                    data-cover="{{ $rel->cover_url }}"
                                    data-audio="{{ $rel->audio_url }}"
                                    data-duration="{{ $rel->duration }}"
                                    data-favorited="{{ $relFav ? 'true' : 'false' }}">
                                <i class="fa-solid fa-play" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-muted small">Tidak ada lagu terkait.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add to Playlist -->
@auth
<div class="modal fade" id="addToPlaylistModal" tabindex="-1" aria-labelledby="addToPlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white" id="addToPlaylistModalLabel">
                    <i class="fa-solid fa-list-ul text-cyan me-2"></i> Tambahkan ke Playlist
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($userPlaylists->count() > 0)
                    <p class="text-secondary small mb-3">Pilih playlist untuk menyimpan <strong>{{ $music->title }}</strong>:</p>
                    <div class="list-group">
                        @foreach($userPlaylists as $pl)
                            <form action="{{ route('playlists.add-music', $pl->id) }}" method="POST" class="mb-2">
                                @csrf
                                <input type="hidden" name="music_id" value="{{ $music->id }}">
                                <button type="submit" class="btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center p-3 text-white">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-list text-cyan"></i>
                                        <span class="fw-semibold">{{ $pl->name }}</span>
                                    </div>
                                    <span class="badge bg-secondary">{{ $pl->music()->count() }} lagu</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-secondary small mb-3">Anda belum memiliki playlist.</p>
                        <a href="{{ route('playlists.create') }}" class="btn btn-cyan btn-sm">Buat Playlist Sekarang</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Report Music -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('music.report', $music->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white" id="reportModalLabel">
                        <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Laporkan Masalah Musik
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary small mb-3">Laporkan jika lagu ini melanggar aturan hak cipta, file rusak, atau konten tidak pantas.</p>
                    
                    <div class="mb-3">
                        <label for="reason_category" class="form-label">Kategori Laporan</label>
                        <select name="reason_category" id="reason_category" class="form-select" required>
                            <option value="copyright">Hak Cipta / Copyright</option>
                            <option value="broken">Musik Tidak Berfungsi / Audio Rusak</option>
                            <option value="inappropriate">Konten Tidak Pantas / SARA</option>
                            <option value="wrong_info">Informasi Musik Salah</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="report_description" class="form-label">Detail Masalah</label>
                        <textarea name="description" id="report_description" rows="3" class="form-control" required placeholder="Jelaskan detail laporan Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth
@endsection

