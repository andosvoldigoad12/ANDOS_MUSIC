<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_image',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path('storage/' . $this->cover_image))) {
            return asset('storage/' . $this->cover_image);
        }
        // If has music, use first music cover
        $firstMusic = $this->music()->first();
        if ($firstMusic && $firstMusic->cover_image && file_exists(public_path('storage/' . $firstMusic->cover_image))) {
            return asset('storage/' . $firstMusic->cover_image);
        }
        return asset('images/default-playlist.svg');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function music(): BelongsToMany
    {
        return $this->belongsToMany(Music::class, 'playlist_music')
                    ->withPivot('order')
                    ->orderByPivot('order', 'asc')
                    ->withTimestamps();
    }
}
