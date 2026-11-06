@extends('layouts.admin')

@section('title', 'Kelola Pengguna - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-users text-cyan me-2"></i> Kelola Pengguna</h2>
            <p class="text-secondary small mb-0">Daftar pengguna terdaftar, status akun, dan statistik aktivitas karya mereka.</p>
        </div>
        <div class="text-secondary small">
            Total Pengguna: <strong class="text-cyan">{{ $users->total() }}</strong>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau email pengguna...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">Filter</button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Foto</th>
                        <th>Nama & Email</th>
                        <th>Role</th>
                        <th class="text-center">Upload Musik</th>
                        <th class="text-center">Playlist</th>
                        <th>Status Akun</th>
                        <th>Terdaftar</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $user->name }}</div>
                                <div class="text-secondary small">{{ $user->email }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></td>
                            <td class="text-center fw-semibold text-white">{{ $user->music_count }}</td>
                            <td class="text-center fw-semibold text-white">{{ $user->playlists_count }}</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1">Dinonaktifkan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun {{ $user->name }}?')">
                                    @csrf
                                    @if($user->status === 'active')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Nonaktifkan Akun">
                                            <i class="fa-solid fa-ban me-1"></i> Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Aktifkan Akun">
                                            <i class="fa-solid fa-check me-1"></i> Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

