@extends('layouts.app')

@section('title', 'Buat Playlist Baru - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="am-card p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="brand-logo-icon" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-list-plus"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-white mb-0">Buat Playlist Baru</h3>
                        <p class="text-secondary small mb-0">Atur lagu-lagu kesukaanmu ke dalam daftar putar personal.</p>
                    </div>
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

                <form action="{{ route('playlists.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Playlist <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Coding & Chill Beats">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Playlist (Opsional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Tuliskan suasana atau tema playlist ini...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="cover_image" class="form-label">Cover Playlist Kustom (Opsional)</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                        <div class="form-text text-muted">Format: JPG, PNG, WEBP, SVG (Maks. 2MB). Jika dikosongkan, akan otomatis menggunakan cover lagu pertama.</div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary small" for="is_public">
                            Jadikan playlist ini Publik (dapat dilihat oleh pengguna lain)
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('playlists.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-cyan px-4 py-2">
                            <i class="fa-solid fa-check me-1"></i> Buat Playlist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

