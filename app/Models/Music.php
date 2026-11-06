<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Music extends Model
{
    use HasFactory;

    protected $table = 'music';

    protected $fillable = [
        'user_id',
        'title',
        'artist',
        'album',
        'genre',
        'description',
        'cover_image',
        'audio_file',
        'duration',
        'status',
        'rejection_reason',
        'plays_count',
    ];

    protected $casts = [
        'duration' => 'integer',
        'plays_count' => 'integer',
    ];

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path('storage/' . $this->cover_image))) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-album.svg');
    }

    public function getAudioUrlAttribute(): string
    {
        return asset('storage/' . $this->audio_file);
    }

    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration ?? 0;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    // Helper
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    // Relationships
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_music')
                    ->withPivot('order')
                    ->withTimestamps();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MusicReport::class);
    }

    public function playHistories(): HasMany
    {
        return $this->hasMany(PlayHistory::class);
    }
}
