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
        // Update vehicles with empty allowed_positions arrays to null
        \DB::statement("UPDATE vehicles SET allowed_positions = NULL WHERE allowed_positions = '[]' OR allowed_positions = 'null'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to empty arrays (though this isn't really necessary)
        \DB::statement("UPDATE vehicles SET allowed_positions = '[]' WHERE allowed_positions IS NULL");
    }
};
