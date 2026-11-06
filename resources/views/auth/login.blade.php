@extends('layouts.app')

@section('title', 'Masuk ke AndosMusic')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="am-card shadow-lg p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-logo-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="fa-solid fa-compact-disc"></i>
                </div>
                <h3 class="fw-bold text-white mb-1">Masuk ke <span style="color: var(--primary-cyan);">AndosMusic</span></h3>
                <p class="text-secondary small">Streaming musik, buat playlist, & bagikan karyamu.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger p-3 small mb-4" style="background-color: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #EF4444;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-input border-color text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-input border-color text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary small" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-cyan w-100 py-2 mb-3">
                    <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Masuk Sekarang
                </button>
            </form>

            <div class="text-center pt-3 border-top" style="border-color: var(--border-color) !important;">
                <p class="text-secondary small mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-cyan fw-bold">Daftar sekarang</a></p>
            </div>

            <!-- Quick Demo Credentials helper box -->
            <div class="mt-4 p-3 rounded" style="background-color: var(--bg-card-hover); border: 1px dashed var(--border-color);">
                <div class="text-cyan fw-bold small mb-2"><i class="fa-solid fa-key me-1"></i> Demo Akun Pengujian:</div>
                <div class="small text-secondary mb-1">
                    <strong class="text-white">Super Admin:</strong> <code>superadmin@andosmusic.com</code> / <code>Admin123!</code>
                </div>
                <div class="small text-secondary mb-1">
                    <strong class="text-white">Admin:</strong> <code>admin@andosmusic.com</code> / <code>Admin123!</code>
                </div>
                <div class="small text-secondary">
                    <strong class="text-white">User:</strong> <code>user@andosmusic.com</code> / <code>User123!</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

