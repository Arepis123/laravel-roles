<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckupTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'vehicle_type',
        'checkup_type',
        'applicable_checks',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'applicable_checks' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function checkupLogs(): HasMany
    {
        return $this->hasMany(VehicleCheckupLog::class, 'template_id');
    }

    // All available check fields
    public static function getAllAvailableChecks(): array
    {
        return [
            'exterior' => [
                'exterior_body_condition' => 'Body Condition',
                'exterior_lights' => 'Lights',
                'exterior_mirrors' => 'Mirrors',
                'exterior_windshield' => 'Windshield',
                'exterior_tires' => 'Tires',
            ],
            'interior' => [
                'interior_seats_seatbelts' => 'Seats & Seatbelts',
                'interior_dashboard' => 'Dashboard',
                'interior_horn' => 'Horn',
                'interior_wipers' => 'Wipers',
                'interior_ac' => 'AC/Heating',
                'interior_cleanliness' => 'Cleanliness',
            ],
            'engine' => [
                'engine_oil' => 'Engine Oil',
                'engine_coolant' => 'Coolant',
                'engine_brake_fluid' => 'Brake Fluid',
                'engine_battery' => 'Battery',
                'engine_washer_fluid' => 'Windshield Washer Fluid',
            ],
            'functional' => [
                'functional_brakes' => 'Brakes',
                'functional_steering' => 'Steering',
                'functional_transmission' => 'Transmission',
                'functional_emergency_kit' => 'Emergency Kit',
            ],
        ];
    }

    // Get flat array of all check field names
    public static function getAllCheckFields(): array
    {
        $all = [];
        foreach (self::getAllAvailableChecks() as $category => $checks) {
            $all = array_merge($all, array_keys($checks));
        }
        return $all;
    }

    // Helper: Check if a specific field is applicable in this template
    public function isCheckApplicable(string $checkField): bool
    {
        return in_array($checkField, $this->applicable_checks ?? []);
    }

    // Helper: Get vehicle type label
    public function getVehicleTypeLabel(): string
    {
        return match($this->vehicle_type) {
            'car' => 'Car',
            'motorcycle' => 'Motorcycle',
            'van' => 'Van',
            'truck' => 'Truck',
            'all' => 'All Vehicles',
            default => ucfirst($this->vehicle_type),
        };
    }

    // Helper: Get checkup type label
    public function getCheckupTypeLabel(): string
    {
        return match($this->checkup_type) {
            'pre_trip' => 'Pre-Trip',
            'post_trip' => 'Post-Trip',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'annual' => 'Annual',
            'all' => 'All Types',
            default => ucfirst($this->checkup_type),
        };
    }

    // Scope: Get default template for specific vehicle/checkup type
    public function scopeDefaultFor($query, string $vehicleType, string $checkupType)
    {
        return $query->where('is_active', true)
            ->where('is_default', true)
            ->where(function($q) use ($vehicleType) {
                $q->where('vehicle_type', $vehicleType)
                  ->orWhere('vehicle_type', 'all');
            })
            ->where(function($q) use ($checkupType) {
                $q->where('checkup_type', $checkupType)
                  ->orWhere('checkup_type', 'all');
            })
            ->orderByRaw("CASE
                WHEN vehicle_type = ? THEN 1
                ELSE 2
            END", [$vehicleType])
            ->orderByRaw("CASE
                WHEN checkup_type = ? THEN 1
                ELSE 2
            END", [$checkupType]);
    }
}
