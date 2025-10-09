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
        Schema::table('vehicle_checkup_logs', function (Blueprint $table) {
            $table->boolean('engine_washer_fluid')->default(true)->after('engine_battery');
            $table->text('engine_washer_fluid_notes')->nullable()->after('engine_washer_fluid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_checkup_logs', function (Blueprint $table) {
            $table->dropColumn(['engine_washer_fluid', 'engine_washer_fluid_notes']);
        });
    }
};
