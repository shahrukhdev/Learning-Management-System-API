<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->string('status', 20);

            $table->foreignId('marked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['student_id', 'academic_class_id', 'attendance_date'],
                'attendance_student_class_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
