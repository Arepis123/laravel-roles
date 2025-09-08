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
        Schema::create('vehicle_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->enum('maintenance_type', ['service', 'repair', 'inspection', 'oil_change', 'tire_change', 'brake_service', 'other'])->default('service');
            $table->string('title')->comment('Brief title of maintenance work');
            $table->text('description')->comment('Detailed description of maintenance work');
            $table->decimal('cost', 10, 2)->nullable()->comment('Cost of maintenance in currency');
            $table->string('performed_by')->comment('Company or person who performed maintenance');
            $table->integer('odometer_at_maintenance')->nullable()->comment('Odometer reading during maintenance');
            $table->date('performed_at')->comment('Date when maintenance was performed');
            $table->date('next_maintenance_due')->nullable()->comment('Next scheduled maintenance date');
            $table->integer('next_maintenance_km')->nullable()->comment('Next maintenance due at this odometer reading');
            $table->json('parts_replaced')->nullable()->comment('JSON array of parts that were replaced');
            $table->string('warranty_until')->nullable()->comment('Warranty expiry for this maintenance');
            $table->foreignId('recorded_by')->constrained('users')->comment('User who recorded this maintenance');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['vehicle_id', 'performed_at']);
            $table->index(['vehicle_id', 'maintenance_type']);
            $table->index(['next_maintenance_due']);
            $table->index(['recorded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_logs');
    }
};