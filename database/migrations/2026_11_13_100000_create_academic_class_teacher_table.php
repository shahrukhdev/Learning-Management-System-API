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
        Schema::create('academic_class_teacher', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['teacher_id', 'academic_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_class_teacher');
    }
};
