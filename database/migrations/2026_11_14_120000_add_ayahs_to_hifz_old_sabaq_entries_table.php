<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Old Sabaq now records the exact ayah range, like New Sabaq. Required by
     * validation for new / updated sessions; nullable here because existing
     * Old Sabaq entries were saved without ayahs.
     */
    public function up(): void
    {
        Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('from_ayah')->nullable()->after('from_surah_id');
            $table->unsignedSmallInteger('to_ayah')->nullable()->after('to_surah_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
            $table->dropColumn(['from_ayah', 'to_ayah']);
        });
    }
};
