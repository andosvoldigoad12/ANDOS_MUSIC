@extends('layouts.admin')

@section('title', 'Kelola Admin - AndosMusic Super Admin')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-user-tie text-cyan me-2"></i> Kelola Staf Admin</h2>
            <p class="text-secondary small mb-0">Kelola akun administrator yang bertugas mengurasi konten dan moderasi musik.</p>
        </div>
        <button type="button" class="btn btn-cyan btn-sm" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Admin Baru
        </button>
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
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th class="text-center" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>
                                <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $admin->name }}</div>
                                <div class="text-secondary small">{{ $admin->email }}</div>
                            </td>
                            <td><span class="badge bg-cyan text-dark fw-bold">ADMIN</span></td>
                            <td>
                                @if($admin->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Dinonaktifkan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $admin->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <!-- Toggle Status -->
                                    <form action="{{ route('admin.users.toggle', $admin->id) }}" method="POST" onsubmit="return confirm('Ubah status akun {{ $admin->name }}?')">
                                        @csrf
                                        @if($admin->status === 'active')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Nonaktifkan">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Aktifkan">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>

                                <!-- Edit Admin Modal -->
                                <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <form action="{{ route('superadmin.admins.update', $admin->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-white">Edit Admin: {{ $admin->name }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lengkap</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alamat Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Password Baru (Opsional)</label>
                                                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-cyan btn-sm">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada akun Admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center">
        {{ $admins->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal: Tambah Admin Baru -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('superadmin.admins.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white" id="createAdminModalLabel">
                        <i class="fa-solid fa-user-plus text-cyan me-2"></i> Tambah Admin Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Moderator Musik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="admin@andosmusic.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-cyan btn-sm">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

