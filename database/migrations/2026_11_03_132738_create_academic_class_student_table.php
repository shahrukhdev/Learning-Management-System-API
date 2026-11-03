
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_class_student', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['student_id', 'academic_class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_class_student');
    }
};
