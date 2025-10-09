<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_checkup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Inspector/User
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');

            $table->enum('checkup_type', ['pre_trip', 'post_trip', 'periodic', 'annual'])->default('pre_trip');
            $table->integer('odometer_reading')->nullable();
            $table->decimal('fuel_level', 5, 2)->nullable()->comment('Fuel level in percentage or liters');

            // Exterior Checks
            $table->boolean('exterior_body_condition')->default(true);
            $table->text('exterior_body_notes')->nullable();
            $table->boolean('exterior_lights')->default(true);
            $table->text('exterior_lights_notes')->nullable();
            $table->boolean('exterior_mirrors')->default(true);
            $table->text('exterior_mirrors_notes')->nullable();
            $table->boolean('exterior_windshield')->default(true);
            $table->text('exterior_windshield_notes')->nullable();
            $table->boolean('exterior_tires')->default(true);
            $table->text('exterior_tires_notes')->nullable();

            // Interior Checks
            $table->boolean('interior_seats_seatbelts')->default(true);
            $table->text('interior_seats_seatbelts_notes')->nullable();
            $table->boolean('interior_dashboard')->default(true);
            $table->text('interior_dashboard_notes')->nullable();
            $table->boolean('interior_horn')->default(true);
            $table->text('interior_horn_notes')->nullable();
            $table->boolean('interior_wipers')->default(true);
            $table->text('interior_wipers_notes')->nullable();
            $table->boolean('interior_ac')->default(true);
            $table->text('interior_ac_notes')->nullable();
            $table->boolean('interior_cleanliness')->default(true);
            $table->text('interior_cleanliness_notes')->nullable();

            // Under Hood Checks
            $table->boolean('engine_oil')->default(true);
            $table->text('engine_oil_notes')->nullable();
            $table->boolean('engine_coolant')->default(true);
            $table->text('engine_coolant_notes')->nullable();
            $table->boolean('engine_brake_fluid')->default(true);
            $table->text('engine_brake_fluid_notes')->nullable();
            $table->boolean('engine_battery')->default(true);
            $table->text('engine_battery_notes')->nullable();

            // Functional Tests
            $table->boolean('functional_brakes')->default(true);
            $table->text('functional_brakes_notes')->nullable();
            $table->boolean('functional_steering')->default(true);
            $table->text('functional_steering_notes')->nullable();
            $table->boolean('functional_transmission')->default(true);
            $table->text('functional_transmission_notes')->nullable();
            $table->boolean('functional_emergency_kit')->default(true);
            $table->text('functional_emergency_kit_notes')->nullable();

            // Overall Assessment
            $table->enum('overall_status', ['approved', 'approved_with_notes', 'rejected', 'needs_maintenance'])->default('approved');
            $table->text('general_notes')->nullable();
            $table->json('photos')->nullable(); // Store array of photo paths

            $table->timestamp('checked_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('user_id');
            $table->index('checkup_type');
            $table->index('overall_status');
            $table->index('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_checkup_logs');
    }
};
