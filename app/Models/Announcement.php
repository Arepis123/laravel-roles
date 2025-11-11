<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'type',
        'priority',
        'is_active',
        'show_banner',
        'show_toast',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_banner' => 'boolean',
        'show_toast' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the user who created this announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all views for this announcement
     */
    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class);
    }

    /**
     * Get all images for this announcement
     */
    public function images(): HasMany
    {
        return $this->hasMany(AnnouncementImage::class)->orderBy('order');
    }

    /**
     * Scope to get only active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope to get banner announcements
     */
    public function scopeBanner($query)
    {
        return $query->where('show_banner', true);
    }

    /**
     * Scope to get toast announcements
     */
    public function scopeToast($query)
    {
        return $query->where('show_toast', true);
    }

    /**
     * Check if announcement is currently visible
     */
    public function isVisible(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if user has viewed this announcement
     */
    public function hasBeenViewedBy(User $user): bool
    {
        return $this->views()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user has read this announcement
     */
    public function hasBeenReadBy(User $user): bool
    {
        return $this->views()->where('user_id', $user->id)->where('is_read', true)->exists();
    }

    /**
     * Mark as viewed by user
     */
    public function markAsViewedBy(User $user): void
    {
        $this->views()->firstOrCreate(
            ['user_id' => $user->id],
            ['viewed_at' => now()]
        );
    }

    /**
     * Mark as read by user
     */
    public function markAsReadBy(User $user): void
    {
        $this->views()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_read' => true,
                'viewed_at' => now()
            ]
        );
    }

    /**
     * Get total views count
     */
    public function getTotalViewsAttribute(): int
    {
        return $this->views()->count();
    }

    /**
     * Get total reads count
     */
    public function getTotalReadsAttribute(): int
    {
        return $this->views()->where('is_read', true)->count();
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'info' => 'blue',
            'warning' => 'amber',
            'success' => 'lime',
            'danger' => 'red',
            default => 'zinc'
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'zinc',
            'normal' => 'blue',
            'high' => 'amber',
            'urgent' => 'red',
            default => 'zinc'
        };
    }

    /**
     * Get secure URL for the main image
     */
    public function getSecureImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        $encodedPath = base64_encode($this->image_path);
        return route('announcements.image.show', ['imagePath' => $encodedPath]);
    }
}
