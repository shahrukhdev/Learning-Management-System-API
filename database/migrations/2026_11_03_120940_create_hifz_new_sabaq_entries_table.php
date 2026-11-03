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
        Schema::create('hifz_new_sabaq_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hifz_session_id')
                ->constrained('hifz_sessions')
                ->cascadeOnDelete();

            $table->foreignId('from_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('from_ayah');

            $table->foreignId('to_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('to_ayah');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hifz_new_sabaq_entries');
    }
};
