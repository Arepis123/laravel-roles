<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavoriteAsset extends Model
{
    protected $fillable = [
        'user_id',
        'favorable_type',
        'favorable_id',
    ];

    /**
     * Get the user who favorited the asset
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favorable model (Vehicle, MeetingRoom, ItAsset)
     */
    public function favorable(): MorphTo
    {
        return $this->morphTo();
    }
}
