<?php

namespace App\Models;

use App\Traits\HasQrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasQrCode;

    protected $fillable = ['model', 'plate_number', 'capacity', 'driver_name', 'notes', 'status', 'allowed_positions', 'allowed_users', 'parking_required', 'qr_code_identifier'];

    protected $casts = [
        'capacity' => 'integer',
        'allowed_positions' => 'array',
        'allowed_users' => 'array',
        'parking_required' => 'boolean'
    ];

    /**
     * Original morphMany relationship for bookings
     */
    public function bookings()
    {
        return $this->morphMany(Booking::class, 'asset');
    }

    /**
     * New relationships to normalized logging tables
     */
    public function fuelLogs(): HasMany
    {
        return $this->hasMany(VehicleFuelLog::class);
    }

    public function odometerLogs(): HasMany
    {
        return $this->hasMany(VehicleOdometerLog::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceLog::class);
    }

    /**
     * Convenience relationships for latest records
     */
    public function latestFuelLog()
    {
        return $this->hasOne(VehicleFuelLog::class)->latestOfMany('filled_at');
    }

    public function latestOdometerLog()
    {
        return $this->hasOne(VehicleOdometerLog::class)->latestOfMany('recorded_at');
    }

    public function latestMaintenanceLog()
    {
        return $this->hasOne(VehicleMaintenanceLog::class)->latestOfMany('performed_at');
    }

    /**
     * Accessor methods for vehicle analytics
     */
    public function getTotalFuelConsumedAttribute()
    {
        return VehicleFuelLog::getTotalFuelForVehicle($this->id);
    }

    public function getTotalFuelCostAttribute()
    {
        return VehicleFuelLog::getTotalCostForVehicle($this->id);
    }

    public function getTotalDistanceTraveledAttribute()
    {
        return VehicleOdometerLog::getTotalDistanceForVehicle($this->id);
    }

    public function getTotalMaintenanceCostAttribute()
    {
        return VehicleMaintenanceLog::getTotalCostForVehicle($this->id);
    }

    public function getCurrentOdometerReadingAttribute()
    {
        $latest = VehicleOdometerLog::getLatestReadingForVehicle($this->id);
        return $latest ? $latest->odometer_reading : null;
    }

    public function getAverageFuelEfficiencyAttribute()
    {
        return VehicleFuelLog::getAverageFuelEfficiency($this->id);
    }

    public function getUpcomingMaintenanceAttribute()
    {
        return VehicleMaintenanceLog::getUpcomingMaintenanceForVehicle($this->id);
    }

    public function getOverdueMaintenanceAttribute()
    {
        return VehicleMaintenanceLog::getOverdueMaintenanceForVehicle($this->id);
    }

    /**
     * Scopes for filtering vehicles
     */
    public function scopeWithFuelData($query)
    {
        return $query->with(['fuelLogs' => function($q) {
            $q->orderBy('filled_at', 'desc');
        }]);
    }

    public function scopeWithOdometerData($query)
    {
        return $query->with(['odometerLogs' => function($q) {
            $q->orderBy('recorded_at', 'desc');
        }]);
    }

    public function scopeWithMaintenanceData($query)
    {
        return $query->with(['maintenanceLogs' => function($q) {
            $q->orderBy('performed_at', 'desc');
        }]);
    }

    public function scopeWithLatestLogs($query)
    {
        return $query->with(['latestFuelLog', 'latestOdometerLog', 'latestMaintenanceLog']);
    }

    /**
     * Helper methods for reports and analytics
     */
    public function getFuelDataForPeriod($startDate, $endDate)
    {
        return [
            'total_fuel' => VehicleFuelLog::getTotalFuelForVehicle($this->id, $startDate, $endDate),
            'total_cost' => VehicleFuelLog::getTotalCostForVehicle($this->id, $startDate, $endDate),
            'average_efficiency' => VehicleFuelLog::getAverageFuelEfficiency($this->id, $startDate, $endDate),
            'fuel_sessions' => $this->fuelLogs()->inDateRange($startDate, $endDate)->count()
        ];
    }

    public function getOdometerDataForPeriod($startDate, $endDate)
    {
        return [
            'total_distance' => VehicleOdometerLog::getTotalDistanceForVehicle($this->id, $startDate, $endDate),
            'average_distance_per_trip' => VehicleOdometerLog::getAverageDistancePerTrip($this->id, $startDate, $endDate),
            'odometer_range' => VehicleOdometerLog::getOdometerRange($this->id, $startDate, $endDate)
        ];
    }

    public function getMaintenanceDataForPeriod($startDate, $endDate)
    {
        return [
            'total_cost' => VehicleMaintenanceLog::getTotalCostForVehicle($this->id, $startDate, $endDate),
            'maintenance_count' => VehicleMaintenanceLog::getMaintenanceCountByType($this->id, $startDate, $endDate),
            'cost_per_km' => VehicleMaintenanceLog::getCostPerKilometer($this->id, $this->total_distance_traveled)
        ];
    }

    /**
     * Position-based vehicle access methods
     */
    public function isAvailableForPosition($position)
    {
        // If allowed_positions is null or empty array, available to all positions
        if (is_null($this->allowed_positions) || empty($this->allowed_positions)) {
            return true;
        }
        
        return in_array($position, $this->allowed_positions);
    }
    
    public function canUserBook($user)
    {
        $hasPositionAccess = $this->isAvailableForPosition($user->position);
        $hasUserAccess = false;
        
        // Check if user is specifically allowed
        if (!is_null($this->allowed_users) && !empty($this->allowed_users)) {
            $hasUserAccess = in_array($user->id, $this->allowed_users);
        }
        
        // If no position restrictions and no specific users, allow all
        $hasNoPositionRestrictions = is_null($this->allowed_positions) || empty($this->allowed_positions);
        $hasNoUserRestrictions = is_null($this->allowed_users) || empty($this->allowed_users);
        
        if ($hasNoPositionRestrictions && $hasNoUserRestrictions) {
            return true;
        }
        
        // Allow if user has position access OR is specifically allowed
        return $hasPositionAccess || $hasUserAccess;
    }
    
    public function getAllowedPositionsText()
    {
        if (is_null($this->allowed_positions) || empty($this->allowed_positions)) {
            return 'All positions';
        }
        
        return implode(', ', $this->allowed_positions);
    }
    
    public function getAllowedUsersText()
    {
        if (is_null($this->allowed_users) || empty($this->allowed_users)) {
            return null;
        }
        
        $users = User::whereIn('id', $this->allowed_users)->get();
        return $users->pluck('name')->implode(', ');
    }
    
    public static function getAvailablePositions()
    {
        return ['CEO', 'Manager', 'Executive', 'Non-executive'];
    }
    
    public function scopeAvailableForPosition($query, $position)
    {
        return $query->where(function($q) use ($position) {
            $q->whereNull('allowed_positions')
              ->orWhere('allowed_positions', '[]')
              ->orWhere('allowed_positions', 'null')
              ->orWhereJsonContains('allowed_positions', $position);
        });
    }

    /**
     * Get comprehensive vehicle statistics for reports
     */
    public function getVehicleStats($startDate = null, $endDate = null)
    {
        return [
            'vehicle_info' => [
                'id' => $this->id,
                'model' => $this->model,
                'plate_number' => $this->plate_number,
                'capacity' => $this->capacity,
                'driver_name' => $this->driver_name,
                'status' => $this->status ?? 'active'
            ],
            'fuel_data' => $this->getFuelDataForPeriod($startDate, $endDate),
            'odometer_data' => $this->getOdometerDataForPeriod($startDate, $endDate),
            'maintenance_data' => $this->getMaintenanceDataForPeriod($startDate, $endDate),
            'booking_stats' => [
                'total_bookings' => $this->getBookingsInDateRange($startDate, $endDate)->count(),
                'completed_bookings' => $this->getBookingsInDateRange($startDate, $endDate)->where('status', 'done')->count()
            ]
        ];
    }
    
    /**
     * Get bookings in date range
     */
    public function getBookingsInDateRange($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return $this->bookings();
        }

        return $this->bookings()->where(function($q) use ($startDate, $endDate) {
            $q->whereDate('start_time', '>=', $startDate)
              ->whereDate('start_time', '<=', $endDate)
              ->orWhereDate('end_time', '>=', $startDate)
              ->whereDate('end_time', '<=', $endDate)
              ->orWhere(function($dateQ) use ($startDate, $endDate) {
                  $dateQ->whereDate('start_time', '<=', $startDate)
                        ->whereDate('end_time', '>=', $endDate);
              });
        });
    }

    /**
     * Check if vehicle has ongoing maintenance
     */
    public function hasOngoingMaintenance()
    {
        return $this->maintenanceLogs()
            ->where('status', 'ongoing')
            ->exists();
    }

    /**
     * Check if vehicle has scheduled maintenance that conflicts with booking dates
     */
    public function hasScheduledMaintenanceInPeriod($startTime, $endTime)
    {
        return $this->maintenanceLogs()
            ->where('status', 'scheduled')
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('performed_at', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          // Check if maintenance spans across the booking period
                          $q->where('performed_at', '<=', $startTime)
                            ->whereNotNull('next_maintenance_due')
                            ->where('next_maintenance_due', '>=', $endTime);
                      });
            })
            ->exists();
    }

    /**
     * Check if vehicle is available for booking (including maintenance checks)
     */
    public function isAvailableForBooking($startTime, $endTime)
    {
        // Check if vehicle status allows booking
        if ($this->status === 'inactive' || $this->status === 'maintenance') {
            return false;
        }

        // Check for ongoing maintenance
        if ($this->hasOngoingMaintenance()) {
            return false;
        }

        // Check for scheduled maintenance conflicts
        if ($this->hasScheduledMaintenanceInPeriod($startTime, $endTime)) {
            return false;
        }

        // Check for existing booking conflicts
        $conflictingBookings = $this->bookings()
            ->whereIn('status', ['pending', 'approved', 'ongoing'])
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    // Booking starts before our end time and ends after our start time
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$conflictingBookings;
    }

    /**
     * Get maintenance status for display
     */
    public function getMaintenanceStatusAttribute()
    {
        $ongoingMaintenance = $this->maintenanceLogs()
            ->where('status', 'ongoing')
            ->latest('performed_at')
            ->first();

        if ($ongoingMaintenance) {
            return [
                'status' => 'ongoing',
                'message' => 'Vehicle under maintenance',
                'maintenance' => $ongoingMaintenance
            ];
        }

        $upcomingMaintenance = $this->maintenanceLogs()
            ->where('status', 'scheduled')
            ->where('performed_at', '>', now())
            ->orderBy('performed_at')
            ->first();

        if ($upcomingMaintenance) {
            return [
                'status' => 'scheduled',
                'message' => 'Maintenance scheduled for ' . $upcomingMaintenance->performed_at->format('M j, Y g:i A'),
                'maintenance' => $upcomingMaintenance
            ];
        }

        return [
            'status' => 'available',
            'message' => 'Available for booking',
            'maintenance' => null
        ];
    }
}

