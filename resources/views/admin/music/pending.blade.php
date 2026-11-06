@extends('layouts.admin')

@section('title', 'Persetujuan Upload Musik (Moderasi) - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-hourglass-half text-warning me-2"></i> Persetujuan Upload Musik</h2>
            <p class="text-secondary small mb-0">Daftar karya musik yang diunggah oleh pengguna dan menunggu verifikasi moderator.</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold">
            {{ $pendingTracks->total() }} Lagu Menunggu Review
        </span>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Cover</th>
                        <th>Judul & Artis</th>
                        <th>Genre & Durasi</th>
                        <th>Uploader</th>
                        <th style="width: 250px;">Dengarkan Audio</th>
                        <th>Tanggal Upload</th>
                        <th class="text-center" style="width: 170px;">Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingTracks as $music)
                        <tr>
                            <td>
                                <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $music->title }}</div>
                                <div class="text-secondary small">{{ $music->artist }}</div>
                                @if($music->album)
                                    <div class="text-muted" style="font-size: 0.72rem;">Album: {{ $music->album }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="genre-tag">{{ $music->genre }}</span>
                                <div class="text-muted small mt-1">{{ $music->formatted_duration }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $music->uploader->avatar_url }}" alt="{{ $music->uploader->name }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold text-white small">{{ $music->uploader->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $music->uploader->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <audio controls class="w-100" style="height: 36px;">
                                    <source src="{{ $music->audio_url }}" type="audio/mpeg">
                                    <source src="{{ $music->audio_url }}" type="audio/wav">
                                    Browser tidak mendukung pemutar audio.
                                </audio>
                            </td>
                            <td class="text-muted small">{{ $music->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <!-- Approve Button -->
                                    <form action="{{ route('admin.music.approve', $music->id) }}" method="POST" onsubmit="return confirm('Setujui dan publikasikan musik ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3" title="Setujui (Approve)">
                                            <i class="fa-solid fa-check me-1"></i> Terima
                                        </button>
                                    </form>

                                    <!-- Reject Modal Trigger -->
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $music->id }}" title="Tolak (Reject)">
                                        <i class="fa-solid fa-xmark me-1"></i> Tolak
                                    </button>
                                </div>

                                <!-- Reject Modal with Reason Input -->
                                <div class="modal fade" id="rejectModal{{ $music->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('admin.music.reject', $music->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger">
                                                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Tolak Musik '{{ $music->title }}'
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-secondary small mb-3">Tuliskan alasan penolakan secara jelas agar pengguna mengetahui hal yang perlu diperbaiki:</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" rows="3" class="form-control" required placeholder="Contoh: Kualitas audio pecah, melanggar hak cipta label tertentu, atau judul tidak sesuai..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak Musik</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-circle-check text-success fs-1 mb-2 d-block"></i>
                                <h6 class="text-white fw-bold">Semua Musik Telah Di-review!</h6>
                                <p class="text-secondary small mb-0">Tidak ada berkas musik baru yang sedang menunggu persetujuan moderator.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $pendingTracks->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

