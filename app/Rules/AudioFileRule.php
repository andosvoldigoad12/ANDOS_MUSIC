<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class AudioFileRule implements ValidationRule
{
    /**
     * Format ekstensi audio yang didukung secara resmi.
     */
    protected array $allowedExtensions = ['mp3', 'wav', 'ogg'];

    /**
     * Whitelist lengkap seluruh variasi MIME type untuk audio MP3, WAV, dan OGG
     * dari berbagai sistem operasi, browser, dan audio encoder.
     */
    protected array $allowedMimeTypes = [
        // MP3 MIME types
        'audio/mpeg',
        'audio/mp3',
        'audio/x-mpeg',
        'audio/x-mp3',
        'audio/mpeg3',
        'audio/x-mpeg3',
        'audio/mpg',
        'audio/x-mpg',

        // WAV MIME types
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/vnd.wave',
        'audio/x-pn-wav',

        // OGG MIME types (termasuk application/ogg dan audio/vorbis yang sering dihasilkan finfo PHP)
        'audio/ogg',
        'application/ogg',
        'audio/vorbis',
        'audio/x-vorbis',
        'audio/x-vorbis+ogg',
        'video/ogg',
        'audio/x-ogg',
        'audio/opus',
        'audio/x-opus',
        'application/x-ogg',

        // Fallback generic stream pada lingkungan tertentu
        'application/octet-stream',
    ];

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail('Berkas audio tidak valid atau gagal diunggah ke server.');
            return;
        }

        // 1. Validasi Ekstensi Berkas (Hanya MP3, WAV, OGG)
        $extension = strtolower($value->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions, true)) {
            $fail('Format berkas audio tidak didukung. Format yang diperbolehkan hanya MP3, WAV, atau OGG.');
            return;
        }

        // 2. Validasi Batas Ukuran Maksimal 30 MB (30 * 1024 * 1024 bytes)
        $maxBytes = 30 * 1024 * 1024;
        if ($value->getSize() > $maxBytes) {
            $fail('Ukuran berkas audio melebihi batas maksimal 30 MB.');
            return;
        }

        // 3. Validasi Tipe MIME
        $mimeType = strtolower((string) $value->getMimeType());
        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            $fail('Tipe MIME berkas audio tidak dikenali sebagai format audio yang valid (terdeteksi: ' . $mimeType . '). Pastikan file merupakan audio MP3, WAV, atau OGG.');
            return;
        }

        // 4. Validasi Keamanan Konten (Mencegah script injection / executable yang disamarkan)
        $realPath = $value->getRealPath();
        if ($realPath && file_exists($realPath)) {
            $handle = @fopen($realPath, 'rb');
            if ($handle) {
                $head = fread($handle, 512);
                fclose($handle);

                // Tolak berkas yang mengandung skrip executable PHP atau tag skrip
                if (
                    stripos($head, '<?php') !== false ||
                    stripos($head, '<?=') !== false ||
                    stripos($head, '<script') !== false
                ) {
                    $fail('Berkas audio ditolak karena mengandung kode skrip yang tidak diizinkan.');
                    return;
                }

                // Untuk application/octet-stream, verifikasi magic bytes audio
                if ($mimeType === 'application/octet-stream') {
                    $hasValidAudioHeader = false;
                    if ($extension === 'wav' && str_starts_with($head, 'RIFF') && str_contains(substr($head, 8, 4), 'WAVE')) {
                        $hasValidAudioHeader = true;
                    } elseif ($extension === 'ogg' && str_starts_with($head, 'OggS')) {
                        $hasValidAudioHeader = true;
                    } elseif ($extension === 'mp3' && (str_starts_with($head, 'ID3') || (ord($head[0] ?? '') === 0xFF && (ord($head[1] ?? '') & 0xE0) === 0xE0))) {
                        $hasValidAudioHeader = true;
                    }

                    if (!$hasValidAudioHeader) {
                        $fail('Struktur berkas audio tidak valid atau rusak.');
                        return;
                    }
                }
            }
        }
    }
}

