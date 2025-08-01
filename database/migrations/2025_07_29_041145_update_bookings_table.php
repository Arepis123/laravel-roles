<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type'); // 'meeting_room', 'vehicle', 'it_asset'
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('booked_by');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('purpose')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
