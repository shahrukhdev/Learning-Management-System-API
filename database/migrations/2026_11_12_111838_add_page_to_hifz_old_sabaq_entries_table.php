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
        Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
            Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
                $table->unsignedSmallInteger('page')->nullable()->after('to_surah_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
            Schema::table('hifz_old_sabaq_entries', function (Blueprint $table) {
                $table->dropColumn('page');
            });
        });
    }
};
