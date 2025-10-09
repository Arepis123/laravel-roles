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
        Schema::create('checkup_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Motorcycle Pre-Trip", "Car Annual Inspection"
            $table->string('description')->nullable();
            $table->enum('vehicle_type', ['car', 'motorcycle', 'van', 'truck', 'all'])->default('all');
            $table->enum('checkup_type', ['pre_trip', 'post_trip', 'weekly', 'monthly', 'annual', 'all'])->default('all');

            // Store applicable checks as JSON array
            $table->json('applicable_checks')->comment('Array of check field names that apply to this template');

            $table->boolean('is_default')->default(false)->comment('Default template for this vehicle/checkup type combination');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('vehicle_type');
            $table->index('checkup_type');
            $table->index('is_active');
        });

        // Add template_id to vehicle_checkup_logs to track which template was used
        Schema::table('vehicle_checkup_logs', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('booking_id')->constrained('checkup_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_checkup_logs', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });

        Schema::dropIfExists('checkup_templates');
    }
};
