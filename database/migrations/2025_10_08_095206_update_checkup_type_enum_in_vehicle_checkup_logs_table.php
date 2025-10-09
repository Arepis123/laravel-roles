<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MariaDB/MySQL, we need to alter the enum column
        DB::statement("ALTER TABLE vehicle_checkup_logs MODIFY COLUMN checkup_type ENUM('pre_trip', 'post_trip', 'weekly', 'monthly', 'annual') NOT NULL DEFAULT 'pre_trip'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE vehicle_checkup_logs MODIFY COLUMN checkup_type ENUM('pre_trip', 'post_trip', 'periodic', 'annual') NOT NULL DEFAULT 'pre_trip'");
    }
};
