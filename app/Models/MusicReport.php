<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'music_id',
        'reason_category',
        'description',
        'status',
        'admin_notes',
        'handled_by',
    ];

    public static function getCategories(): array
    {
        return [
            'copyright' => 'Hak Cipta / Copyright',
            'broken' => 'Musik Tidak Berfungsi / Audio Rusak',
            'inappropriate' => 'Konten Tidak Pantas / Sara / Eksplisit',
            'wrong_info' => 'Informasi Musik Salah (Judul/Artist/Genre)',
            'other' => 'Lainnya',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}

