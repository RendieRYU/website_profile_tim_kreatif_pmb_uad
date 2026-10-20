<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('events', 'links')) {
            Schema::table('events', function (Blueprint $table) {
                $table->json('links')->nullable()->after('link');
            });
        }

        // Migrasikan data link lama ke format JSON links jika ada
        $events = DB::table('events')->whereNotNull('link')->get();
        foreach ($events as $event) {
            if ($event->link) {
                $links = [
                    ['title' => 'Tautan Utama', 'url' => $event->link]
                ];
                DB::table('events')->where('id', $event->id)->update([
                    'links' => json_encode($links)
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'links')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('links');
            });
        }
    }
};
