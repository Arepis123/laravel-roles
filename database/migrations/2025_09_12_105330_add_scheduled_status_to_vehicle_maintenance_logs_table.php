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
        Schema::table('vehicle_maintenance_logs', function (Blueprint $table) {
            // Update the enum to include 'scheduled' status
            $table->enum('status', ['ongoing', 'completed', 'scheduled'])->default('ongoing')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_maintenance_logs', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing')->change();
        });
    }
};