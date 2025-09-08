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
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            // Add performed_by field
            $table->string('performed_by')->nullable()->after('recorded_by')->comment('Person who performed the reading');
            
            // Make booking_id nullable (it was not nullable in the original migration)
            $table->foreignId('booking_id')->nullable()->change();
        });

        // Update the reading_type enum values to match the component
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            $table->dropColumn('reading_type');
        });

        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            $table->enum('reading_type', ['start', 'end', 'manual', 'service'])->default('manual')->after('odometer_reading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            // Remove performed_by field
            $table->dropColumn('performed_by');
            
            // Revert booking_id to not nullable
            $table->foreignId('booking_id')->nullable(false)->change();
        });

        // Revert reading_type enum values
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            $table->dropColumn('reading_type');
        });

        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            $table->enum('reading_type', ['start', 'end', 'maintenance', 'inspection'])->default('end')->after('odometer_reading');
        });
    }
};