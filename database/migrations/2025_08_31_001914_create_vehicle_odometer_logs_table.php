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
        Schema::create('vehicle_odometer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->integer('odometer_reading')->comment('Odometer reading in kilometers');
            $table->enum('reading_type', ['start', 'end', 'maintenance', 'inspection'])->default('end');
            $table->integer('distance_traveled')->nullable()->comment('Distance traveled since last reading');
            $table->foreignId('recorded_by')->constrained('users')->comment('User who recorded the reading');
            $table->timestamp('recorded_at')->useCurrent()->comment('When the reading was recorded');
            $table->text('notes')->nullable()->comment('Additional notes about odometer reading');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['vehicle_id', 'recorded_at']);
            $table->index(['vehicle_id', 'reading_type']);
            $table->index(['booking_id']);
            $table->index(['recorded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_odometer_logs');
    }
};