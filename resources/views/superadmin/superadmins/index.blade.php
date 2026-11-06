@extends('layouts.admin')

@section('title', 'Kelola Super Admin - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-user-shield text-warning me-2"></i> Kelola Super Admin</h2>
            <p class="text-secondary small mb-0">Manajemen akun tingkat eksekutif dengan perlindungan keamanan anti-lockout.</p>
        </div>
        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#createSuperAdminModal">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Super Admin Baru
        </button>
    </div>

    <!-- Security Safeguard Notice -->
    <div class="alert alert-cyan p-3 mb-4 rounded-3 small" style="background: rgba(0, 217, 232, 0.1); border: 1px solid rgba(0, 217, 232, 0.3); color: var(--primary-cyan);">
        <i class="fa-solid fa-shield-halved me-1"></i> <strong>Perlindungan Keamanan Aktif:</strong> Sistem secara otomatis melarang Super Admin menonaktifkan dirinya sendiri atau menonaktifkan akun Super Admin aktif terakhir untuk mencegah terkuncinya akses sistem (system lockout).
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Foto</th>
                        <th>Nama & Email</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th class="text-center" style="width: 170px;">Status Akses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($superAdmins as $sa)
                        @php
                            $isSelf = auth()->id() === $sa->id;
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $sa->avatar_url }}" alt="{{ $sa->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-white">
                                    {{ $sa->name }}
                                    @if($isSelf)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">AKUN ANDA</span>
                                    @endif
                                </div>
                                <div class="text-secondary small">{{ $sa->email }}</div>
                            </td>
                            <td>
                                @if($sa->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Dinonaktifkan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $sa->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                @if($isSelf)
                                    <button class="btn btn-secondary btn-sm" disabled title="Perlindungan: Tidak dapat menonaktifkan akun sendiri">
                                        <i class="fa-solid fa-lock me-1"></i> Akun Anda
                                    </button>
                                @else
                                    <form action="{{ route('superadmin.superadmins.toggle', $sa->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status Super Admin {{ $sa->name }}?')">
                                        @csrf
                                        @if($sa->status === 'active')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Nonaktifkan Super Admin">
                                                <i class="fa-solid fa-ban me-1"></i> Nonaktifkan
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Aktifkan Super Admin">
                                                <i class="fa-solid fa-check me-1"></i> Aktifkan
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Tambah Super Admin Baru -->
<div class="modal fade" id="createSuperAdminModal" tabindex="-1" aria-labelledby="createSuperAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content">
            <form action="{{ route('superadmin.superadmins.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white" id="createSuperAdminModalLabel">
                        <i class="fa-solid fa-user-shield text-warning me-2"></i> Tambah Super Admin Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Chief Administrator">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="superadmin@andosmusic.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold btn-sm">Simpan Super Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

