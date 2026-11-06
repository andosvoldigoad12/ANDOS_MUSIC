@extends('layouts.app')

@section('title', 'Edit Musik - ' . $music->title)

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="am-card p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="brand-logo-icon" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-white mb-0">Edit Informasi Musik</h3>
                        <p class="text-secondary small mb-0">Perbarui metadata karya musik '{{ $music->title }}'.</p>
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

                <form action="{{ route('music.update', $music->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Judul Musik</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $music->title) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="artist" class="form-label">Nama Artist</label>
                            <input type="text" name="artist" id="artist" class="form-control" value="{{ old('artist', $music->artist) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="album" class="form-label">Album</label>
                            <input type="text" name="album" id="album" class="form-control" value="{{ old('album', $music->album) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="genre" class="form-label">Genre</label>
                            <select name="genre" id="genre" class="form-select" required>
                                @foreach(['Pop', 'Rock', 'Electronic', 'EDM', 'Hip-Hop', 'R&B', 'Jazz', 'Acoustic', 'Indie', 'Lo-Fi', 'Metal', 'Classical', 'Lainnya'] as $g)
                                    <option value="{{ $g }}" {{ old('genre', $music->genre) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Replace Cover Art -->
                    <div class="my-4">
                        <label for="cover_image" class="form-label">Ganti Cover Album</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $music->cover_url }}" alt="Cover" class="rounded-2" style="width: 50px; height: 50px; object-fit: cover;">
                            <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                        </div>
                        <div class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah cover.</div>
                    </div>

                    <!-- Replace Audio File -->
                    <div class="mb-4">
                        <label for="audio_file" class="form-label">Ganti Berkas Audio (Opsional)</label>
                        <input type="file" name="audio_file" id="audio_file" class="form-control" accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg,application/ogg">
                        <div class="form-text text-muted">Format yang didukung: MP3, WAV, OGG (Maksimal 30 MB). Biarkan kosong jika tidak ingin mengganti file lagu.</div>
                        <div id="editAudioErrorMsg" class="mt-2 text-danger small fw-semibold d-none"></div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Deskripsi / Lirik</label>
                        <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $music->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('music.show', $music->id) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-cyan px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const editAudioInput = document.getElementById('audio_file');
    const editAudioError = document.getElementById('editAudioErrorMsg');

    if (editAudioInput) {
        editAudioInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                editAudioError.classList.add('d-none');
                return;
            }

            const allowedExts = ['mp3', 'wav', 'ogg'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            const maxBytes = 30 * 1024 * 1024;

            if (!allowedExts.includes(fileExt)) {
                editAudioError.textContent = `Format file .${fileExt} tidak didukung. Harap pilih file audio MP3, WAV, atau OGG.`;
                editAudioError.classList.remove('d-none');
                editAudioInput.value = '';
                return;
            }

            if (file.size > maxBytes) {
                editAudioError.textContent = `Ukuran file (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal 30 MB.`;
                editAudioError.classList.remove('d-none');
                editAudioInput.value = '';
                return;
            }

            editAudioError.classList.add('d-none');
        });
    }
</script>
@endpush

