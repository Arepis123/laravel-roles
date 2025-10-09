<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCheckupLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'booking_id',
        'template_id',
        'checkup_type',
        'odometer_reading',
        'fuel_level',

        // Exterior
        'exterior_body_condition',
        'exterior_body_notes',
        'exterior_lights',
        'exterior_lights_notes',
        'exterior_mirrors',
        'exterior_mirrors_notes',
        'exterior_windshield',
        'exterior_windshield_notes',
        'exterior_tires',
        'exterior_tires_notes',

        // Interior
        'interior_seats_seatbelts',
        'interior_seats_seatbelts_notes',
        'interior_dashboard',
        'interior_dashboard_notes',
        'interior_horn',
        'interior_horn_notes',
        'interior_wipers',
        'interior_wipers_notes',
        'interior_ac',
        'interior_ac_notes',
        'interior_cleanliness',
        'interior_cleanliness_notes',

        // Engine
        'engine_oil',
        'engine_oil_notes',
        'engine_coolant',
        'engine_coolant_notes',
        'engine_brake_fluid',
        'engine_brake_fluid_notes',
        'engine_battery',
        'engine_battery_notes',
        'engine_washer_fluid',
        'engine_washer_fluid_notes',

        // Functional
        'functional_brakes',
        'functional_brakes_notes',
        'functional_steering',
        'functional_steering_notes',
        'functional_transmission',
        'functional_transmission_notes',
        'functional_emergency_kit',
        'functional_emergency_kit_notes',

        // Overall
        'overall_status',
        'general_notes',
        'photos',
        'checked_at',
    ];

    protected $casts = [
        'exterior_body_condition' => 'boolean',
        'exterior_lights' => 'boolean',
        'exterior_mirrors' => 'boolean',
        'exterior_windshield' => 'boolean',
        'exterior_tires' => 'boolean',
        'interior_seats_seatbelts' => 'boolean',
        'interior_dashboard' => 'boolean',
        'interior_horn' => 'boolean',
        'interior_wipers' => 'boolean',
        'interior_ac' => 'boolean',
        'interior_cleanliness' => 'boolean',
        'engine_oil' => 'boolean',
        'engine_coolant' => 'boolean',
        'engine_brake_fluid' => 'boolean',
        'engine_battery' => 'boolean',
        'engine_washer_fluid' => 'boolean',
        'functional_brakes' => 'boolean',
        'functional_steering' => 'boolean',
        'functional_transmission' => 'boolean',
        'functional_emergency_kit' => 'boolean',
        'photos' => 'array',
        'checked_at' => 'datetime',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CheckupTemplate::class, 'template_id');
    }

    // Helper Methods
    public function hasIssues(): bool
    {
        return $this->overall_status === 'rejected' || $this->overall_status === 'needs_maintenance';
    }

    public function getCheckupTypeLabel(): string
    {
        return match($this->checkup_type) {
            'pre_trip' => 'Pre-Trip',
            'post_trip' => 'Post-Trip',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'annual' => 'Annual',
            default => ucfirst($this->checkup_type),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->overall_status) {
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'approved_with_notes' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            'needs_maintenance' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        };
    }

    public function getFailedChecks(): array
    {
        $failed = [];
        $checks = [
            'exterior_body_condition' => 'Body Condition',
            'exterior_lights' => 'Lights',
            'exterior_mirrors' => 'Mirrors',
            'exterior_windshield' => 'Windshield',
            'exterior_tires' => 'Tires',
            'interior_seats_seatbelts' => 'Seats & Seatbelts',
            'interior_dashboard' => 'Dashboard',
            'interior_horn' => 'Horn',
            'interior_wipers' => 'Wipers',
            'interior_ac' => 'AC/Heating',
            'interior_cleanliness' => 'Cleanliness',
            'engine_oil' => 'Engine Oil',
            'engine_coolant' => 'Coolant',
            'engine_brake_fluid' => 'Brake Fluid',
            'engine_battery' => 'Battery',
            'functional_brakes' => 'Brakes',
            'functional_steering' => 'Steering',
            'functional_transmission' => 'Transmission',
            'functional_emergency_kit' => 'Emergency Kit',
        ];

        foreach ($checks as $field => $label) {
            if (!$this->$field) {
                $failed[] = $label;
            }
        }

        return $failed;
    }
}
