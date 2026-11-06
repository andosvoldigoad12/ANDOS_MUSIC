@extends('layouts.app')

@section('title', 'Upload Musik Saya - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-cloud-arrow-up text-cyan me-2"></i> Upload Musik Saya</h2>
            <p class="text-secondary small mb-0">Pantau status moderasi dan kelola seluruh karya musik yang telah Anda unggah.</p>
        </div>
        <a href="{{ route('music.create') }}" class="btn btn-cyan btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Upload Lagu Baru
        </a>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Cover</th>
                        <th>Judul & Artis</th>
                        <th>Genre</th>
                        <th>Durasi</th>
                        <th>Status Moderasi</th>
                        <th>Jumlah Putar</th>
                        <th>Tanggal Upload</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uploads as $music)
                        <tr>
                            <td>
                                <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 44px; height: 44px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $music->title }}</div>
                                <div class="text-secondary small">{{ $music->artist }}</div>
                            </td>
                            <td><span class="genre-tag">{{ $music->genre }}</span></td>
                            <td class="text-muted small">{{ $music->formatted_duration }}</td>
                            <td>
                                @if($music->status === 'approved')
                                    <span class="badge-status badge-approved">
                                        <i class="fa-solid fa-circle-check"></i> Disetujui
                                    </span>
                                @elseif($music->status === 'pending')
                                    <span class="badge-status badge-pending">
                                        <i class="fa-solid fa-hourglass-half"></i> Menunggu Review
                                    </span>
                                @elseif($music->status === 'rejected')
                                    <button class="badge-status badge-rejected border-0" type="button" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $music->id }}" title="Klik untuk melihat alasan penolakan">
                                        <i class="fa-solid fa-circle-xmark"></i> Ditolak (Lihat Alasan)
                                    </button>

                                    <!-- Rejection Reason Modal -->
                                    <div class="modal fade" id="reasonModal{{ $music->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Alasan Penolakan</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-secondary small mb-2">Moderator memberikan catatan penolakan untuk musik <strong>{{ $music->title }}</strong>:</p>
                                                    <div class="p-3 rounded-2" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #EF4444;">
                                                        {{ $music->rejection_reason ?: 'Tidak ada alasan tertulis yang diberikan.' }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                    <a href="{{ route('music.edit', $music->id) }}" class="btn btn-cyan btn-sm">Perbaiki & Ajukan Ulang</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold text-cyan">{{ number_format($music->plays_count) }}</td>
                            <td class="text-muted small">{{ $music->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if($music->status === 'approved')
                                        <a href="{{ route('music.show', $music->id) }}" class="btn btn-outline-cyan btn-sm" title="Lihat Halaman Lagu">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('music.edit', $music->id) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('music.destroy', $music->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lagu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fa-solid fa-cloud-arrow-up text-muted fs-2 mb-3 d-block"></i>
                                <h6 class="text-white fw-bold">Belum ada musik yang diunggah</h6>
                                <p class="text-secondary small mb-3">Mulai unggah lagu orisinalmu sekarang dan nikmati karya musikmu bersama yang lain.</p>
                                <a href="{{ route('music.create') }}" class="btn btn-cyan btn-sm">Upload Musik Sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $uploads->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

