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
        Schema::create('hifz_manzil_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hifz_session_id')
                ->constrained('hifz_sessions')
                ->cascadeOnDelete();

            $table->foreignId('from_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->foreignId('to_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->enum('status', [
                'fluent',
                'mistakes',
                'repeat',
                'needs-revision',
            ])->nullable();

            $table->enum('strength', [
                'strong',
                'good',
                'needs-work',
                'weak',
            ])->nullable();

            $table->unsignedSmallInteger('mistakes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hifz_manzil_entries');
    }
};
