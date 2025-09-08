<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenanceLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'title',
        'description',
        'cost',
        'performed_by',
        'service_provider',
        'odometer_at_maintenance',
        'performed_at',
        'next_maintenance_due',
        'next_maintenance_km',
        'parts_replaced',
        'warranty_until',
        'recorded_by',
        'notes',
        'status'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'odometer_at_maintenance' => 'integer',
        'next_maintenance_km' => 'integer',
        'performed_at' => 'datetime',
        'next_maintenance_due' => 'date',
        'parts_replaced' => 'array'
    ];

    /**
     * Relationships
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
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
        return $query->whereBetween('performed_at', [$startDate, $endDate]);
    }

    public function scopeByMaintenanceType($query, $maintenanceType)
    {
        return $query->where('maintenance_type', $maintenanceType);
    }

    public function scopeDueForMaintenance($query, $date = null)
    {
        $date = $date ?: now()->toDateString();
        return $query->where('next_maintenance_due', '<=', $date);
    }

    public function scopeWithWarranty($query)
    {
        return $query->whereNotNull('warranty_until')
            ->where('warranty_until', '>', now()->toDateString());
    }

    public function scopeExpiredWarranty($query)
    {
        return $query->whereNotNull('warranty_until')
            ->where('warranty_until', '<=', now()->toDateString());
    }

    /**
     * Accessors
     */
    public function getIsUnderWarrantyAttribute()
    {
        return $this->warranty_until && $this->warranty_until > now()->toDateString();
    }

    public function getIsDueForMaintenanceAttribute()
    {
        return $this->next_maintenance_due && $this->next_maintenance_due <= now()->toDateString();
    }

    public function getDaysUntilMaintenanceAttribute()
    {
        if (!$this->next_maintenance_due) {
            return null;
        }
        
        return now()->diffInDays($this->next_maintenance_due, false);
    }

    public function getMaintenanceStatusAttribute()
    {
        if (!$this->next_maintenance_due) {
            return 'no_schedule';
        }
        
        $daysUntil = $this->days_until_maintenance;
        
        if ($daysUntil < 0) {
            return 'overdue';
        } elseif ($daysUntil <= 7) {
            return 'due_soon';
        } elseif ($daysUntil <= 30) {
            return 'upcoming';
        }
        
        return 'scheduled';
    }

    public function getFormattedPartsReplacedAttribute()
    {
        if (!$this->parts_replaced || !is_array($this->parts_replaced)) {
            return 'N/A';
        }
        
        return implode(', ', $this->parts_replaced);
    }

    /**
     * Static helper methods
     */
    public static function getTotalCostForVehicle($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->whereNotNull('cost')->sum('cost');
    }

    public static function getMaintenanceCountByType($vehicleId, $startDate = null, $endDate = null)
    {
        $query = static::forVehicle($vehicleId);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->selectRaw('maintenance_type, COUNT(*) as count')
            ->groupBy('maintenance_type')
            ->pluck('count', 'maintenance_type')
            ->toArray();
    }

    public static function getLatestMaintenanceForVehicle($vehicleId)
    {
        return static::forVehicle($vehicleId)
            ->orderBy('performed_at', 'desc')
            ->first();
    }

    public static function getUpcomingMaintenanceForVehicle($vehicleId, $days = 30)
    {
        return static::forVehicle($vehicleId)
            ->whereNotNull('next_maintenance_due')
            ->whereBetween('next_maintenance_due', [
                now()->toDateString(),
                now()->addDays($days)->toDateString()
            ])
            ->orderBy('next_maintenance_due')
            ->get();
    }

    public static function getOverdueMaintenanceForVehicle($vehicleId)
    {
        return static::forVehicle($vehicleId)
            ->whereNotNull('next_maintenance_due')
            ->where('next_maintenance_due', '<', now()->toDateString())
            ->orderBy('next_maintenance_due')
            ->get();
    }

    public static function getCostPerKilometer($vehicleId, $totalKilometers)
    {
        if (!$totalKilometers || $totalKilometers <= 0) {
            return null;
        }
        
        $totalCost = static::getTotalCostForVehicle($vehicleId);
        
        return $totalCost > 0 ? round($totalCost / $totalKilometers, 4) : null;
    }
}