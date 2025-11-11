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
        Schema::create('booking_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->integer('hours_after_end')->default(1)->comment('Hours after booking end time to send reminder');
            $table->string('frequency')->default('hourly')->comment('How often to check: hourly, daily, every_4_hours');
            $table->boolean('send_to_passengers')->default(false)->comment('Send reminders to passengers as well');
            $table->integer('max_reminders')->default(3)->comment('Maximum number of reminders to send per booking');
            $table->text('custom_message')->nullable()->comment('Custom message to include in reminder email');
            $table->json('excluded_asset_types')->nullable()->comment('Asset types to exclude from reminders');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('booking_reminder_settings')->insert([
            'enabled' => true,
            'hours_after_end' => 1,
            'frequency' => 'hourly',
            'send_to_passengers' => false,
            'max_reminders' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reminder_settings');
    }
};
