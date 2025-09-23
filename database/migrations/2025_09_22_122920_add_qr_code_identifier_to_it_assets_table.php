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
        Schema::table('it_assets', function (Blueprint $table) {
            $table->string('qr_code_identifier')->nullable()->unique()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_assets', function (Blueprint $table) {
            $table->dropColumn('qr_code_identifier');
        });
    }
};
