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
        Schema::create('vehicle_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->decimal('fuel_amount', 8, 2)->comment('Amount of fuel filled in liters');
            $table->enum('fuel_type', ['petrol', 'diesel', 'hybrid', 'electric'])->default('petrol');
            $table->decimal('fuel_cost', 10, 2)->nullable()->comment('Cost of fuel in currency');
            $table->string('fuel_station')->nullable()->comment('Name or location of fuel station');
            $table->integer('odometer_at_fill')->nullable()->comment('Odometer reading when fuel was filled');
            $table->foreignId('filled_by')->constrained('users')->comment('User who filled the fuel');
            $table->timestamp('filled_at')->useCurrent()->comment('When the fuel was filled');
            $table->text('notes')->nullable()->comment('Additional notes about fuel filling');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['vehicle_id', 'filled_at']);
            $table->index(['booking_id']);
            $table->index(['filled_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_fuel_logs');
    }
};