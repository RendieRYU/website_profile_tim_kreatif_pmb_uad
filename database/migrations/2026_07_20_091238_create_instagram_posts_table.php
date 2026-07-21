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
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal_posting');
            $table->string('link_konten')->nullable();
            $table->string('jenis_konten')->default('FEED'); // REELS, FEED, STORY, dll.
            $table->string('pillar')->nullable();
            $table->integer('reach')->default(0);
            $table->integer('impression_home')->default(0);
            $table->integer('impression_profile')->default(0);
            $table->integer('impression_explore')->default(0);
            $table->integer('impression_hashtag')->default(0);
            $table->integer('impression_other')->default(0);
            $table->decimal('interaction_followers', 5, 2)->default(0.0);
            $table->decimal('interaction_non_followers', 5, 2)->default(0.0);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('saves')->default(0);
            $table->integer('reposts')->default(0);
            $table->integer('new_followers')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_posts');
    }
};
