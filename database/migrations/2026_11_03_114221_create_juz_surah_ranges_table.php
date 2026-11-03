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
        Schema::create('juz_surah_ranges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('juz_id')
                ->constrained('juz')
                ->cascadeOnDelete();

            $table->foreignId('surah_id')
                ->constrained('surahs')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('start_ayah');
            $table->unsignedSmallInteger('end_ayah');

            $table->timestamps();

            $table->unique(['juz_id', 'surah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juz_surah_ranges');
    }
};
