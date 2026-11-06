@extends('layouts.admin')

@section('title', 'Pengaturan Website - AndosMusic Super Admin')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="am-card p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="brand-logo-icon" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-white mb-0">Pengaturan Website</h3>
                        <p class="text-secondary small mb-0">Atur identitas platform, kontak, dan pesan pengumuman global.</p>
                    </div>
                </div>

                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nama Website / Brand</label>
                        <input type="text" name="site_name" id="site_name" class="form-control" value="{{ old('site_name', $settings['site_name']) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tagline" class="form-label">Tagline Platform</label>
                        <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $settings['tagline']) }}" required>
                        <div class="form-text text-muted">Tagline resmi: "Your Music, Your World."</div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email Dukungan Pelanggan</label>
                        <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email']) }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="announcement" class="form-label">Pesan Pengumuman Global (Banner)</label>
                        <textarea name="announcement" id="announcement" rows="3" class="form-control" placeholder="Pesan ini akan ditampilkan di banner atas halaman beranda...">{{ old('announcement', $settings['announcement']) }}</textarea>
                        <div class="form-text text-muted">Kosongkan jika tidak ada pengumuman aktif.</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-cyan px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

