<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['model', 'plate_number', 'capacity', 'driver_name', 'notes', 'status', 'allowed_positions', 'parking_required'];

    protected $casts = [
        'capacity' => 'integer',
        'allowed_positions' => 'array',
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
        return $this->isAvailableForPosition($user->position);
    }
    
    public function getAllowedPositionsText()
    {
        if (is_null($this->allowed_positions) || empty($this->allowed_positions)) {
            return 'All positions';
        }
        
        return implode(', ', $this->allowed_positions);
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
}

