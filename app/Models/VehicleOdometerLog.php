<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleOdometerLog extends Model
{
    protected $fillable = [
        'booking_id',
        'vehicle_id',
        'odometer_reading',
        'reading_type',
        'distance_traveled',
        'recorded_by',
        'recorded_at',
        'notes',
        'performed_by'
    ];

    protected $casts = [
        'odometer_reading' => 'integer',
        'distance_traveled' => 'integer',
        'recorded_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scopes
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereDate('recorded_at', '>=', $startDate)
                     ->whereDate('recorded_at', '<=', $endDate);
    }

    public function scopeByReadingType($query, $readingType)
    {
        return $query->where('reading_type', $readingType);
    }

    public function scopeStartReadings($query)
    {
        return $query->where('reading_type', 'start');
    }

    public function scopeEndReadings($query)
    {
        return $query->where('reading_type', 'end');
    }

    /**
     * Boot method to automatically calculate distance traveled
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($odometerLog) {
            $odometerLog->calculateDistanceTraveled();
        });

        static::updating(function ($odometerLog) {
            $odometerLog->calculateDistanceTraveled();
        });
    }

    /**
     * Calculate distance traveled since last reading
     */
    public function calculateDistanceTraveled()
    {
        if ($this->reading_type === 'start') {
            $this->distance_traveled = 0;
            return;
        }

        $previousReading = static::where('vehicle_id', $this->vehicle_id)
            ->where('recorded_at', '<', $this->recorded_at ?: now())
            ->orderBy('recorded_at', 'desc')
            ->first();

        if ($previousReading) {
            $this->distance_traveled = max(0, $this->odometer_reading - $previousReading->odometer_reading);
        } else {
            $this->distance_traveled = 0;
        }
    }

    /**
     * Static helper methods
     */
    public static function getLatestReadingForVehicle($vehicleId)
    {
        return static::forVehicle($vehicleId)
            ->orderBy('recorded_at', 'desc')
            ->first();
    }

    public static function getTotalDistanceForVehicle($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->sum('distance_traveled');
    }

    public static function getAverageDistancePerTrip($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId)->where('distance_traveled', '>', 0);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        $totalDistance = $query->sum('distance_traveled');
        $tripCount = $query->count();
        
        return $tripCount > 0 ? round($totalDistance / $tripCount, 2) : 0;
    }

    public static function getOdometerRange($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return [
            'min' => $query->min('odometer_reading'),
            'max' => $query->max('odometer_reading'),
            'total_distance' => $query->max('odometer_reading') - $query->min('odometer_reading')
        ];
    }

    /**
     * Get readings grouped by booking
     */
    public static function getReadingsByBooking($vehicleId, $bookingId)
    {
        return static::forVehicle($vehicleId)
            ->where('booking_id', $bookingId)
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('reading_type');
    }
}