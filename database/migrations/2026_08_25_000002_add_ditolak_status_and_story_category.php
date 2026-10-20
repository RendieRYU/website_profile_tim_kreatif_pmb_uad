<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('ide', 'acc', 'selesai', 'ditunda', 'terjadwal', 'ditolak') DEFAULT 'ide'");

        // Add 'Story' category if it doesn't already exist
        Category::firstOrCreate(
            ['name' => 'Story'],
            ['color' => '#F59E0B']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('ide', 'acc', 'selesai', 'ditunda', 'terjadwal') DEFAULT 'ide'");
    }
};
