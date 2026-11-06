@extends('layouts.admin')

@section('title', 'Manajemen Semua Pengguna - AndosMusic Super Admin')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold">SUPER ADMIN ONLY</div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-users-gear text-cyan me-2"></i> Manajemen Semua Pengguna</h2>
            <p class="text-secondary small mb-0">Kelola role, status akun, dan lihat informasi seluruh pengguna (Admin & User).</p>
        </div>
        <div class="text-secondary small">
            Total Ditampilkan: <strong class="text-cyan">{{ $users->total() }}</strong>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau email pengguna...">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">Filter</button>
                @if(request()->hasAny(['q', 'role', 'status']))
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="alert alert-danger p-3 small mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <th class="text-center" style="width: 250px;">Aksi</th>
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
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-cyan text-dark fw-bold">ADMIN</span>
                                @else
                                    <span class="badge bg-secondary">USER</span>
                                @endif
                            </td>
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
                                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                    <!-- Change Role -->
                                    <form action="{{ route('superadmin.users.update-role', $user->id) }}" method="POST" onsubmit="return confirm('Ubah role {{ $user->name }} menjadi {{ $user->role === 'user' ? 'Admin' : 'User' }}?')">
                                        @csrf
                                        @method('PUT')
                                        @if($user->role === 'user')
                                            <input type="hidden" name="role" value="admin">
                                            <button type="submit" class="btn btn-outline-cyan btn-sm" title="Jadikan Admin">
                                                <i class="fa-solid fa-arrow-up me-1"></i> Jadikan Admin
                                            </button>
                                        @else
                                            <input type="hidden" name="role" value="user">
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Turunkan ke User">
                                                <i class="fa-solid fa-arrow-down me-1"></i> Jadikan User
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Toggle Status -->
                                    <form action="{{ route('superadmin.users.toggle', $user->id) }}" method="POST" onsubmit="return confirm('Ubah status akun {{ $user->name }}?')">
                                        @csrf
                                        @if($user->status === 'active')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Nonaktifkan Akun">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Aktifkan Akun">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-users fs-1 mb-3 d-block opacity-25"></i>
                                    <p class="mb-0">Tidak ada pengguna ditemukan.</p>
                                </div>
                            </td>
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

