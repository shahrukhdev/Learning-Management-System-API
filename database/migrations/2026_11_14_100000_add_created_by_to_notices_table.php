<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Who posted the notice ("Posted by"). Nullable: notices created before
     * this column existed have no known author, and deleting the user keeps
     * the notice.
     */
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('is_active')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
