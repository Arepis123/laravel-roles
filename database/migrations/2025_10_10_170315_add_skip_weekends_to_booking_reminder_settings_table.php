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
        Schema::table('booking_reminder_settings', function (Blueprint $table) {
            $table->boolean('skip_weekends')->default(true)->after('send_to_passengers')->comment('Skip sending reminders on weekends');
        });

        // Update existing record
        DB::table('booking_reminder_settings')->update([
            'skip_weekends' => true,
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_reminder_settings', function (Blueprint $table) {
            $table->dropColumn('skip_weekends');
        });
    }
};
