@extends('layouts.admin')

@section('title', 'Kelola Seluruh Musik - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-music text-cyan me-2"></i> Kelola Seluruh Musik</h2>
            <p class="text-secondary small mb-0">Lihat, edit, dan kelola seluruh katalog musik dalam platform AndosMusic.</p>
        </div>
        <a href="{{ route('admin.music.create') }}" class="btn btn-cyan btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Upload Musik Baru
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('admin.music.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari judul musik, artist, genre...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">Filter</button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.music.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Cover</th>
                        <th>Judul & Artis</th>
                        <th>Genre</th>
                        <th>Uploader</th>
                        <th>Status</th>
                        <th>Pemutaran</th>
                        <th>Tanggal</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musicList as $music)
                        <tr>
                            <td>
                                <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 44px; height: 44px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $music->title }}</div>
                                <div class="text-secondary small">{{ $music->artist }}</div>
                            </td>
                            <td><span class="genre-tag">{{ $music->genre }}</span></td>
                            <td class="text-secondary small">{{ $music->uploader->name }}</td>
                            <td>
                                <span class="badge-status badge-{{ $music->status }}">
                                    {{ strtoupper($music->status) }}
                                </span>
                            </td>
                            <td class="fw-bold text-cyan">{{ number_format($music->plays_count) }}</td>
                            <td class="text-muted small">{{ $music->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('music.show', $music->id) }}" class="btn btn-outline-cyan btn-sm" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('music.edit', $music->id) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('music.destroy', $music->id) }}" method="POST" onsubmit="return confirm('Hapus musik ini secara permanen?')">
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
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada musik yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $musicList->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

