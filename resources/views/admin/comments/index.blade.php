@extends('layouts.admin')

@section('title', 'Moderasi Komentar - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-comments text-cyan me-2"></i> Moderasi Komentar</h2>
            <p class="text-secondary small mb-0">Pantau diskusi pengguna dan hapus komentar yang melanggar ketentuan komunitas.</p>
        </div>
        <div class="text-secondary small">
            Total Komentar: <strong class="text-cyan">{{ $comments->total() }}</strong>
        </div>
    </div>

    <!-- Search Form -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari isi komentar...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">Cari</button>
                @if(request('q'))
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Penulis</th>
                        <th>Lagu</th>
                        <th>Isi Komentar</th>
                        <th>Tanggal</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $index => $c)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $c->user->avatar_url }}" alt="{{ $c->user->name }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold text-white small">{{ $c->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $c->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($c->music)
                                    <a href="{{ route('music.show', $c->music->id) }}" target="_blank" class="fw-bold text-white text-decoration-none small hover-cyan">
                                        {{ $c->music->title }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem;"></i>
                                    </a>
                                    <div class="text-secondary" style="font-size: 0.72rem;">{{ $c->music->artist }}</div>
                                @else
                                    <span class="text-muted small">Musik telah dihapus</span>
                                @endif
                            </td>
                            <td class="text-white small" style="max-width: 320px;">
                                {{ $c->content }}
                            </td>
                            <td class="text-muted small">{{ $c->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center">
                                <form action="{{ route('comments.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Komentar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada komentar pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $comments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

