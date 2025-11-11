<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementView extends Model
{
    protected $fillable = [
        'announcement_id',
        'user_id',
        'is_read',
        'viewed_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the announcement
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
