<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Hifz sessions are no longer tied to a teacher record. Instead, the user
     * who recorded the session (Teacher, Admin or Super Admin) is stored in
     * recorded_by. Existing rows are backfilled from teachers.user_id.
     */
    public function up(): void
    {
        Schema::table('hifz_sessions', function (Blueprint $table) {
            $table->foreignId('recorded_by')
                ->nullable()
                ->after('student_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('hifz_sessions')
            ->whereNotNull('teacher_id')
            ->update([
                'recorded_by' => DB::raw(
                    '(select teachers.user_id from teachers where teachers.id = hifz_sessions.teacher_id)'
                ),
            ]);

        Schema::table('hifz_sessions', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hifz_sessions', function (Blueprint $table) {
            $table->foreignId('teacher_id')
                ->nullable()
                ->after('student_id')
                ->constrained('teachers')
                ->restrictOnDelete();
        });

        DB::table('hifz_sessions')
            ->whereNotNull('recorded_by')
            ->update([
                'teacher_id' => DB::raw(
                    '(select teachers.id from teachers where teachers.user_id = hifz_sessions.recorded_by limit 1)'
                ),
            ]);

        Schema::table('hifz_sessions', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->dropColumn('recorded_by');
        });
    }
};
