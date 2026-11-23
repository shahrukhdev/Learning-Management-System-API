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
        Schema::table('hifz_settings', function (Blueprint $table) {
            $table->unsignedInteger('total_ayats')->default(0)->after('total_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hifz_settings', function (Blueprint $table) {
            $table->dropColumn('total_ayats');
        });
    }
};
