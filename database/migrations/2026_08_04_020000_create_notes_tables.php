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
        Schema::create('note_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#E0F2FE');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained('note_folders')->onDelete('set null');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('color')->default('#FEF3C7'); // default soft yellow
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->integer('order')->default(0);
            $table->date('note_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('note_folders');
    }
};
