<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Booking extends Model
{
    protected $fillable = [
        'asset_type',
        'asset_id',
        'booked_by',
        'purpose',
        'start_time',
        'end_time',
        'status',
        'capacity',
        'additional_booking',
        'refreshment_details',
        'status_history',
        'booked_by',
        'status',
        'status_history',
        'passengers', 
        'destination',
        'done_details', // Add this field for storing completion details
    ];
    
    protected $casts = [
        'additional_booking' => 'array',
        'status_history' => 'array',
        'passengers' => 'array', // Cast passengers as array
        'done_details' => 'array', // Cast done_details as array
        'start_time' => 'datetime',
        'end_time' => 'datetime',        
    ];

    public function asset()
    {
        return $this->morphTo(null, 'asset_type', 'asset_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'booked_by');
    // }

    public function getAssetTypeLabelAttribute()
    {
        return match (class_basename($this->asset_type)) {
            'MeetingRoom' => 'Meeting Room',
            'Vehicle'     => 'Vehicle',
            'ItAsset'     => 'IT Asset',
            default       => 'Unknown',
        };
    }  
    
    public function getLatestStatusChangeAttribute()
    {
        $history = $this->status_history ?? [];
        return empty($history) ? null : end($history);
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'approved' => 'bg-green-100 text-green-800 border-green-300',
            'rejected' => 'bg-red-100 text-red-800 border-red-300',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-300',
            'done' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300'
        };
    }

    /**
     * Check if booking status has been changed
     */
    public function hasStatusHistory(): bool
    {
        return !empty($this->status_history);
    }

    /**
     * Get status history count
     */
    public function getStatusHistoryCountAttribute(): int
    {
        return count($this->status_history ?? []);
    }

    /**
     * Check if booking has done details
     */
    public function hasDoneDetails(): bool
    {
        return !empty($this->done_details);
    }

    /**
     * Get done details remarks (for meeting room and IT assets)
     */
    public function getDoneRemarksAttribute(): ?string
    {
        return $this->done_details['remarks'] ?? null;
    }

    /**
     * Get vehicle specific done details
     */
    public function getVehicleDoneDetailsAttribute(): array
    {
        if (class_basename($this->asset_type) !== 'Vehicle') {
            return [];
        }

        return [
            'odometer' => $this->done_details['odometer'] ?? null,
            'gas_filled' => $this->done_details['gas_filled'] ?? false,
            'gas_amount' => $this->done_details['gas_amount'] ?? null,
        ];
    }

    /**
     * Scopes for different statuses
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    /**
     * Check if booking is active (approved and within time range)
     */
    public function isActive(): bool
    {
        return $this->status === 'approved' && 
               $this->start_time <= now() && 
               $this->end_time >= now();
    }

    /**
     * Check if booking is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'approved' && $this->start_time > now();
    }

    /**
     * Check if booking is past
     */
    public function isPast(): bool
    {
        return $this->end_time < now();
    } 

    /** 
     * Get the user who made this booking
     */
    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    /**
     * Alternative method name for consistency
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }
    
    /**
     * Get the passengers for vehicle bookings
     */
    public function passengerUsers()
    {
        if (empty($this->passengers)) {
            return collect();
        }
        
        return User::whereIn('id', $this->passengers)->get();
    }
    
    /**
     * Check if booking has passengers
     */
    public function hasPassengers(): bool
    {
        return !empty($this->passengers) && count($this->passengers) > 0;
    }
    
    /**
     * Get passenger names as a formatted string
     */
    public function getPassengerNamesAttribute(): string
    {
        if (!$this->hasPassengers()) {
            return '';
        }
        
        return $this->passengerUsers()->pluck('name')->implode(', ');
    }
    
    /**
     * Check if a specific user is a passenger in this booking
     */
    public function hasPassenger($userId): bool
    {
        return in_array($userId, $this->passengers ?? []);
    }
}