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
        Schema::create('qr_code_logs', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type'); // App\Models\Vehicle, App\Models\MeetingRoom, etc.
            $table->unsignedBigInteger('asset_id');
            $table->string('qr_identifier'); // The UUID identifier
            $table->string('action'); // 'generated', 'regenerated', 'scanned', 'booking_completed', 'scan_failed'
            $table->unsignedBigInteger('user_id')->nullable(); // Who performed the action
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6 address
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->string('booking_id')->nullable(); // Related booking if applicable
            $table->json('metadata')->nullable(); // Additional data (device info, location, etc.)
            $table->timestamp('scanned_at')->nullable(); // When the QR was scanned (can differ from created_at)
            $table->timestamps();

            // Indexes for better performance
            $table->index(['asset_type', 'asset_id']);
            $table->index(['qr_identifier']);
            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['scanned_at']);
            $table->index(['created_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_logs');
    }
};
