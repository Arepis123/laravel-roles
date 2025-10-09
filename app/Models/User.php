<?php

namespace App\Models;

use App\Models\Booking;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'font_size',
        'status',
        'position',
        'deleted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
            'position' => 'string',
            'deleted' => 'boolean',
        ];
    }

    // Boot method to set default role for new users
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            // Assign 'User' role by default if no roles are assigned
            if ($user->roles()->count() === 0) {
                $user->assignRole('User');
            }
        });
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booked_by');
    }

    /**
     * Get all favorite assets for this user
     */
    public function favoriteAssets()
    {
        return $this->hasMany(UserFavoriteAsset::class);
    }

    /**
     * Check if user has favorited a specific asset
     */
    public function hasFavorited($assetType, $assetId): bool
    {
        return $this->favoriteAssets()
            ->where('favorable_type', $assetType)
            ->where('favorable_id', $assetId)
            ->exists();
    }

    /**
     * Toggle favorite status for an asset
     */
    public function toggleFavorite($assetType, $assetId): void
    {
        $favorite = $this->favoriteAssets()
            ->where('favorable_type', $assetType)
            ->where('favorable_id', $assetId)
            ->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            $this->favoriteAssets()->create([
                'favorable_type' => $assetType,
                'favorable_id' => $assetId,
            ]);
        }
    }

    // Helper method to check if user is active
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Helper method to toggle status
    public function toggleStatus(): void
    {
        $this->status = $this->status === 'active' ? 'inactive' : 'active';
        $this->save();
    }

    // Helper method to check if user is deleted
    public function isDeleted(): bool
    {
        return $this->deleted === true;
    }

    // Helper method to soft delete user
    public function softDelete(): void
    {
        $this->deleted = true;
        $this->save();
    }

    // Helper method to restore user
    public function restore(): void
    {
        $this->deleted = false;
        $this->save();
    }

    // Query scope to exclude deleted users
    public function scopeNotDeleted($query)
    {
        return $query->where('deleted', false);
    }

    // Query scope to get only deleted users
    public function scopeDeleted($query)
    {
        return $query->where('deleted', true);
    }

    // Helper method to get available positions
    public static function getPositions(): array
    {
        return ['CEO', 'Manager', 'Executive', 'Non-executive'];
    }

    // Helper method to check if user is in management position
    public function isManagement(): bool
    {
        return in_array($this->position, ['CEO', 'Manager']);
    }

    // Helper method to get position badge color for UI
    public function getPositionBadgeColor(): string
    {
        return match($this->position) {
            'CEO' => 'bg-purple-100 text-purple-800',
            'Manager' => 'bg-blue-100 text-blue-800',
            'Executive' => 'bg-green-100 text-green-800',
            'Non-executive' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the number of minutes for remember token expiry.
     * Override Laravel's default 5 years to 1 month (43200 minutes).
     */
    public function getRememberTokenValidityDuration(): int
    {
        return config('auth.remember_duration', 43200); // 30 days in minutes
    }
}