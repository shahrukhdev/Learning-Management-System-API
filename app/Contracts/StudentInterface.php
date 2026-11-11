<?php

namespace App\Contracts;

use App\DTOs\Academic\StoreStudentDTO;
use App\DTOs\Academic\UpdateStudentDTO;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;

interface StudentInterface
{
    public function getAllStudents(array $filters = []): LengthAwarePaginator;

    public function createStudent(StoreStudentDTO $dto): Student;

    public function getStudent(int $id): Student;

    public function updateStudent(int $id, UpdateStudentDTO $dto): Student;

    public function deleteStudent(int $id): void;

    public function findByUserId(int $userId): ?Student;

    public function syncClass(Student $student, int $academicClassId): void;
}
