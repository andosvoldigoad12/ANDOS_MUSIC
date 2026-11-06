@extends('layouts.app')

@section('title', 'Daftar Akun Baru - AndosMusic')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-5 col-xl-4">
        <div class="am-card shadow-lg p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-logo-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h3 class="fw-bold text-white mb-1">Buat Akun <span style="color: var(--primary-cyan);">AndosMusic</span></h3>
                <p class="text-secondary small">Bergabunglah dan nikmati pengalaman streaming musik terbaik.</p>
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

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Contoh: Dimas Andos">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="nama@email.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted" style="background-color: var(--bg-input); border-color: var(--border-color);"><i class="fa-solid fa-shield-halved"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password">
                    </div>
                </div>

                <div class="mb-4 small text-secondary">
                    Dengan mendaftar, Anda menyetujui Ketentuan Penggunaan dan Kebijakan Privasi AndosMusic.
                </div>

                <button type="submit" class="btn btn-cyan w-100 py-2 mb-3">
                    <i class="fa-solid fa-circle-check me-1"></i> Daftar Sekarang
                </button>
            </form>

            <div class="text-center pt-3 border-top" style="border-color: var(--border-color) !important;">
                <p class="text-secondary small mb-0">Sudah memiliki akun? <a href="{{ route('login') }}" class="text-cyan fw-bold">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

