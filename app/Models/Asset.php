<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Asset Interface/Trait for unified management
trait AssetManagement
{
    // Common asset statuses
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DAMAGED = 'damaged';
    const STATUS_RETIRED = 'retired';

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_IN_USE => 'In Use',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_DAMAGED => 'Damaged',
            self::STATUS_RETIRED => 'Retired',
        ];
    }

    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBookable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function isAvailableForBooking($startDate, $endDate)
    {
        if ($this->status !== self::STATUS_AVAILABLE) {
            return false;
        }

        return !$this->bookings()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_time', [$startDate, $endDate])
                      ->orWhereBetween('end_time', [$startDate, $endDate])
                      ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                          $subQuery->where('start_time', '<=', $startDate)
                                   ->where('end_time', '>=', $endDate);
                      });
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();
    }

    public function getActiveBookingsCountAttribute()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('end_time', '>=', now())
            ->count();
    }

    // Abstract methods that should be implemented by each model
    abstract public function getAssetType();
    abstract public function getDisplayName();
    abstract public function getIdentifier();
}
