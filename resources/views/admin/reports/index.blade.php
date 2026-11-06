@extends('layouts.admin')

@section('title', 'Laporan Masalah Musik - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Laporan Masalah Musik</h2>
            <p class="text-secondary small mb-0">Tinjau dan tindaklanjuti laporan pengguna terkait hak cipta, file rusak, atau pelanggaran konten.</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-cyan' : 'btn-outline-cyan' }}">Semua</a>
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-cyan' : 'btn-outline-cyan' }}">Pending</a>
            <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="btn btn-sm {{ request('status') === 'resolved' ? 'btn-cyan' : 'btn-outline-cyan' }}">Selesai</a>
            <a href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}" class="btn btn-sm {{ request('status') === 'dismissed' ? 'btn-cyan' : 'btn-outline-cyan' }}">Diabaikan</a>
        </div>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Musik Dilaporkan</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Deskripsi Laporan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-center" style="width: 130px;">Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $rep)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                @if($rep->music)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $rep->music->cover_url }}" alt="{{ $rep->music->title }}" class="rounded-2" style="width: 36px; height: 36px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('music.show', $rep->music->id) }}" target="_blank" class="fw-bold text-white text-decoration-none small hover-cyan">
                                                {{ $rep->music->title }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem;"></i>
                                            </a>
                                            <div class="text-secondary" style="font-size: 0.72rem;">{{ $rep->music->artist }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Musik telah dihapus</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-white small fw-semibold">{{ $rep->user->name }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $rep->user->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                    {{ \App\Models\MusicReport::getCategories()[$rep->reason_category] ?? $rep->reason_category }}
                                </span>
                            </td>
                            <td class="text-secondary small" style="max-width: 250px;">
                                <div class="text-truncate" title="{{ $rep->description }}">{{ $rep->description }}</div>
                                @if($rep->admin_notes)
                                    <div class="text-cyan mt-1" style="font-size: 0.72rem;"><strong>Catatan Admin:</strong> {{ $rep->admin_notes }}</div>
                                @endif
                            </td>
                            <td>
                                @if($rep->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($rep->status === 'resolved')
                                    <span class="badge bg-success">Selesai (Resolved)</span>
                                @else
                                    <span class="badge bg-secondary">Diabaikan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $rep->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-cyan btn-sm" data-bs-toggle="modal" data-bs-target="#handleReportModal{{ $rep->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Tangani
                                </button>

                                <!-- Handle Report Modal -->
                                <div class="modal fade" id="handleReportModal{{ $rep->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.reports.update', $rep->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-white">
                                                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Tangani Laporan #{{ $rep->id }}
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status Tindakan</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="pending" {{ $rep->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="resolved" {{ $rep->status === 'resolved' ? 'selected' : '' }}>Selesai / Ditindaklanjuti (Resolved)</option>
                                                            <option value="dismissed" {{ $rep->status === 'dismissed' ? 'selected' : '' }}>Diabaikan / Tidak Ada Pelanggaran (Dismissed)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Admin</label>
                                                        <textarea name="admin_notes" rows="3" class="form-control" placeholder="Tulis catatan penanganan masalah...">{{ old('admin_notes', $rep->admin_notes) }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-cyan btn-sm">Simpan Keputusan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada laporan masalah musik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

