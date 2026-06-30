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
        // Get the currently active period to assign as default
        $activePeriod = DB::table('periods')->where('is_active', true)->first();
        $defaultPeriodId = $activePeriod ? $activePeriod->id : null;

        Schema::table('metrics', function (Blueprint $table) use ($defaultPeriodId) {
            $table->foreignId('period_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('events', function (Blueprint $table) use ($defaultPeriodId) {
            $table->foreignId('period_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Set the default period for existing data
        if ($defaultPeriodId) {
            DB::table('metrics')->update(['period_id' => $defaultPeriodId]);
            DB::table('events')->update(['period_id' => $defaultPeriodId]);
        }
        
        // Make columns non-nullable after filling default values
        Schema::table('metrics', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable(false)->change();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });
    }
};
