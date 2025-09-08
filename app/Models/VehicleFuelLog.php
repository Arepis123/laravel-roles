<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VehicleFuelLog extends Model
{
    protected $fillable = [
        'booking_id',
        'vehicle_id',
        'fuel_amount',
        'fuel_type',
        'fuel_cost',
        'fuel_station',
        'odometer_at_fill',
        'filled_by',
        'filled_at',
        'notes'
    ];

    protected $casts = [
        'fuel_amount' => 'decimal:2',
        'fuel_cost' => 'decimal:2',
        'odometer_at_fill' => 'integer',
        'filled_at' => 'datetime'
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

    public function filledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by');
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
        return $query->whereBetween('filled_at', [$startDate, $endDate]);
    }

    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Accessors & Mutators
     */
    public function getFuelEfficiencyAttribute()
    {
        if (!$this->odometer_at_fill || !$this->fuel_amount) {
            return null;
        }

        // Get previous fuel log to calculate distance
        $previousLog = static::where('vehicle_id', $this->vehicle_id)
            ->where('filled_at', '<', $this->filled_at)
            ->whereNotNull('odometer_at_fill')
            ->orderBy('filled_at', 'desc')
            ->first();

        if (!$previousLog) {
            return null;
        }

        $distance = $this->odometer_at_fill - $previousLog->odometer_at_fill;
        
        return $distance > 0 ? round($distance / $this->fuel_amount, 2) : null;
    }

    public function getCostPerLiterAttribute()
    {
        return $this->fuel_cost && $this->fuel_amount > 0 
            ? round($this->fuel_cost / $this->fuel_amount, 2) 
            : null;
    }

    /**
     * Static helper methods
     */
    public static function getTotalFuelForVehicle($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->sum('fuel_amount');
    }

    public static function getTotalCostForVehicle($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->whereNotNull('fuel_cost')->sum('fuel_cost');
    }

    public static function getAverageFuelEfficiency($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId)->whereNotNull('odometer_at_fill');
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        $logs = $query->orderBy('filled_at')->get();
        $efficiencies = [];
        
        foreach ($logs as $log) {
            $efficiency = $log->fuel_efficiency;
            if ($efficiency) {
                $efficiencies[] = $efficiency;
            }
        }
        
        return count($efficiencies) > 0 ? round(array_sum($efficiencies) / count($efficiencies), 2) : null;
    }
}