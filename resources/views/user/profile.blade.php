@extends('layouts.app')

@section('title', 'Profil Pengguna - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h2 class="fw-bold text-white mb-1">Pengaturan Profil</h2>
        <p class="text-secondary small">Kelola informasi pribadi dan keamanan akun Anda.</p>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="am-card text-center p-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mb-3 shadow" style="width: 110px; height: 110px; object-fit: cover; border: 3px solid var(--primary-cyan);">
                <h4 class="fw-bold text-white mb-1">{{ $user->name }}</h4>
                <p class="text-secondary small mb-2">{{ $user->email }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-cyan text-dark px-3 py-1">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                    <span class="badge bg-success px-3 py-1">{{ strtoupper($user->status) }}</span>
                </div>
                <p class="text-muted small mb-0">{{ $user->bio ?: 'Belum ada bio pribadi.' }}</p>
            </div>
        </div>

        <!-- Form Edit Profile & Password -->
        <div class="col-lg-8">
            <div class="am-card p-4 mb-4">
                <h5 class="fw-bold text-white mb-4 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                    <i class="fa-solid fa-user-pen text-cyan me-2"></i> Perbarui Informasi Profil
                </h5>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email (Permanen)</label>
                        <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled readonly>
                        <div class="form-text text-muted">Email tidak dapat diubah setelah terdaftar.</div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio Singkat</label>
                        <textarea name="bio" id="bio" rows="3" class="form-control" placeholder="Tuliskan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="avatar" class="form-label">Foto Profil (Avatar)</label>
                        <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                        <div class="form-text text-muted">Format yang didukung: JPG, PNG, WEBP, SVG (Maks. 2MB).</div>
                    </div>

                    <button type="submit" class="btn btn-cyan">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Profil
                    </button>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="am-card p-4">
                <h5 class="fw-bold text-white mb-4 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                    <i class="fa-solid fa-key text-cyan me-2"></i> Ubah Password
                </h5>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="••••••••">
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="new_password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
                    </div>

                    <button type="submit" class="btn btn-outline-cyan">
                        <i class="fa-solid fa-lock me-1"></i> Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

