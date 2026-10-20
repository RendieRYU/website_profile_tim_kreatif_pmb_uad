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
        if (!Schema::hasColumn('instagram_posts', 'custom_fields')) {
            Schema::table('instagram_posts', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('new_followers');
            });
        }

        if (!Schema::hasColumn('tiktok_posts', 'custom_fields')) {
            Schema::table('tiktok_posts', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('new_followers');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('instagram_posts', 'custom_fields')) {
            Schema::table('instagram_posts', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        if (Schema::hasColumn('tiktok_posts', 'custom_fields')) {
            Schema::table('tiktok_posts', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }
    }
};
