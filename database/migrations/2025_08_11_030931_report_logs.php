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
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // 'assets', 'bookings', 'users'
            $table->string('report_format'); // 'pdf', 'excel', 'csv'
            $table->json('filters')->nullable(); // Store filter parameters
            $table->string('file_path')->nullable();
            $table->string('file_name');
            $table->integer('record_count')->default(0);
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};