<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementImage extends Model
{
    protected $fillable = [
        'announcement_id',
        'image_path',
        'order',
    ];

    /**
     * Get the announcement that owns this image
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * Get secure URL for the image
     */
    public function getSecureUrlAttribute(): string
    {
        $encodedPath = base64_encode($this->image_path);
        return route('announcements.image.show', ['imagePath' => $encodedPath]);
    }
}
