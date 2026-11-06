@extends('layouts.app')

@section('title', 'Upload Musik Baru - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="am-card p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="brand-logo-icon" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-white mb-0">Upload Musik Baru</h3>
                        <p class="text-secondary small mb-0">Bagikan karya musik orisinalmu kepada seluruh pendengar di AndosMusic.</p>
                    </div>
                </div>

                @if(auth()->user()->isStaff())
                    <div class="alert alert-cyan p-3 mb-4 rounded-3 small" style="background: rgba(0, 217, 232, 0.1); border: 1px solid rgba(0, 217, 232, 0.3); color: var(--primary-cyan);">
                        <i class="fa-solid fa-shield-halved me-1"></i> Anda login sebagai <strong>{{ strtoupper(auth()->user()->role) }}</strong>. Musik yang Anda unggah akan <strong>langsung dipublikasikan (Approved)</strong>.
                    </div>
                @else
                    <div class="alert alert-warning p-3 mb-4 rounded-3 small" style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); color: #FBBF24;">
                        <i class="fa-solid fa-hourglass-half me-1"></i> Musik yang Anda unggah akan ditinjau oleh tim moderator terlebih dahulu (Status: <strong>Pending</strong>) sebelum dapat didengarkan oleh publik.
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger p-3 small mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('music.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <input type="hidden" name="duration" id="audioDuration" value="180">

                    <!-- Audio File Picker -->
                    <div class="mb-4 p-4 rounded-3 text-center position-relative" style="background-color: var(--bg-input); border: 2px dashed var(--border-color);" id="audioDropArea">
                        <i class="fa-solid fa-file-audio fs-1 text-cyan mb-2 d-block"></i>
                        <h6 class="fw-bold text-white mb-1">Pilih Berkas Audio</h6>
                        <p class="text-secondary small mb-3">Format yang didukung: <strong>MP3, WAV, OGG</strong> (Maksimal 30 MB)</p>
                        
                        <input type="file" name="audio_file" id="audioFileInput" class="form-control" accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg,application/ogg" required>
                        <div id="audioFileName" class="mt-2 text-cyan small fw-semibold"></div>
                        <div id="audioErrorMsg" class="mt-2 text-danger small fw-semibold d-none"></div>
                    </div>

                    <div class="row g-3">
                        <!-- Judul Musik -->
                        <div class="col-md-6">
                            <label for="title" class="form-label">Judul Musik <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: Midnight Memories">
                        </div>

                        <!-- Nama Artist -->
                        <div class="col-md-6">
                            <label for="artist" class="form-label">Nama Artist / Band <span class="text-danger">*</span></label>
                            <input type="text" name="artist" id="artist" class="form-control" value="{{ old('artist', auth()->user()->name) }}" required placeholder="Contoh: The Neon Lights">
                        </div>

                        <!-- Album -->
                        <div class="col-md-6">
                            <label for="album" class="form-label">Nama Album (Opsional)</label>
                            <input type="text" name="album" id="album" class="form-control" value="{{ old('album') }}" placeholder="Kosongkan jika Single">
                        </div>

                        <!-- Genre -->
                        <div class="col-md-6">
                            <label for="genre" class="form-label">Genre Musik <span class="text-danger">*</span></label>
                            <select name="genre" id="genre" class="form-select" required>
                                <option value="">Pilih Genre</option>
                                <option value="Pop" {{ old('genre') === 'Pop' ? 'selected' : '' }}>Pop</option>
                                <option value="Rock" {{ old('genre') === 'Rock' ? 'selected' : '' }}>Rock</option>
                                <option value="Electronic" {{ old('genre') === 'Electronic' ? 'selected' : '' }}>Electronic / Synthwave</option>
                                <option value="EDM" {{ old('genre') === 'EDM' ? 'selected' : '' }}>EDM / Future Bass</option>
                                <option value="Hip-Hop" {{ old('genre') === 'Hip-Hop' ? 'selected' : '' }}>Hip-Hop / Rap</option>
                                <option value="R&B" {{ old('genre') === 'R&B' ? 'selected' : '' }}>R&B / Soul</option>
                                <option value="Jazz" {{ old('genre') === 'Jazz' ? 'selected' : '' }}>Jazz / Blues</option>
                                <option value="Acoustic" {{ old('genre') === 'Acoustic' ? 'selected' : '' }}>Acoustic / Folk</option>
                                <option value="Indie" {{ old('genre') === 'Indie' ? 'selected' : '' }}>Indie</option>
                                <option value="Lo-Fi" {{ old('genre') === 'Lo-Fi' ? 'selected' : '' }}>Lo-Fi</option>
                                <option value="Metal" {{ old('genre') === 'Metal' ? 'selected' : '' }}>Metal</option>
                                <option value="Classical" {{ old('genre') === 'Classical' ? 'selected' : '' }}>Klasik</option>
                                <option value="Lainnya" {{ old('genre') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cover Art Upload -->
                    <div class="my-4">
                        <label for="cover_image" class="form-label">Cover Album / Artwork (Opsional)</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                        <div class="form-text text-muted">Format: JPG, PNG, WEBP, SVG (Maks. 3MB). Jika kosong, akan menggunakan cover default.</div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">Deskripsi / Cerita di Balik Lagu / Lirik</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Ceritakan kisah di balik proses pembuatan lagu ini atau sertakan lirik...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('user.home') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-cyan px-4 py-2" id="submitBtn">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Musik Sekarang
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
    // Audio file client-side validation & duration extraction
    const audioInput = document.getElementById('audioFileInput');
    const durationInput = document.getElementById('audioDuration');
    const audioNameDisplay = document.getElementById('audioFileName');
    const audioErrorDisplay = document.getElementById('audioErrorMsg');
    const submitBtn = document.getElementById('submitBtn');

    if (audioInput) {
        audioInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                audioNameDisplay.textContent = '';
                audioErrorDisplay.classList.add('d-none');
                return;
            }

            const allowedExts = ['mp3', 'wav', 'ogg'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            const maxBytes = 30 * 1024 * 1024; // 30 MB

            // Check extension
            if (!allowedExts.includes(fileExt)) {
                audioErrorDisplay.textContent = `Format file .${fileExt} tidak didukung. Harap pilih file audio MP3, WAV, atau OGG.`;
                audioErrorDisplay.classList.remove('d-none');
                audioNameDisplay.textContent = '';
                audioInput.value = '';
                return;
            }

            // Check size
            if (file.size > maxBytes) {
                audioErrorDisplay.textContent = `Ukuran file (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal 30 MB.`;
                audioErrorDisplay.classList.remove('d-none');
                audioNameDisplay.textContent = '';
                audioInput.value = '';
                return;
            }

            // Valid file
            audioErrorDisplay.classList.add('d-none');
            audioNameDisplay.innerHTML = `<i class="fa-solid fa-circle-check text-success me-1"></i> File siap diunggah: <strong>${file.name}</strong> (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;

            // Extract duration
            const testAudio = new Audio();
            testAudio.src = URL.createObjectURL(file);
            testAudio.addEventListener('loadedmetadata', function() {
                const sec = Math.round(testAudio.duration);
                if (sec && !isNaN(sec)) {
                    durationInput.value = sec;
                }
            });
        });
    }
</script>
@endpush

