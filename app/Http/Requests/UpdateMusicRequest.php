<?php

namespace App\Http\Requests;

use App\Rules\AudioFileRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMusicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'album' => ['nullable', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg', 'max:3072'],
            'audio_file' => ['nullable', 'file', new AudioFileRule()],
        ];
    }

    /**
     * Custom message validation in Indonesian.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul musik wajib diisi.',
            'artist.required' => 'Nama artis atau band wajib diisi.',
            'genre.required' => 'Genre musik wajib dipilih.',
            'cover_image.mimes' => 'Cover album harus berformat JPG, JPEG, PNG, WEBP, atau SVG.',
            'cover_image.max' => 'Ukuran cover album maksimal 3 MB.',
        ];
    }
}

