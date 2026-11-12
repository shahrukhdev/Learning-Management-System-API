<?php

namespace App\Contracts\Academic;

use App\DTOs\Academic\StoreStudentProgressDTO;
use App\Models\Student;
use App\Models\StudentProgress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StudentProgressInterface
{
    /**
     * Academic class IDs assigned to the (active) teacher profile of the given user.
     *
     * @return array<int, int>
     */
    public function getTeacherClassIds(int $userId): array;

    /**
     * @param  array<int, int>|null  $classIds  null = no class restriction
     */
    public function getStudents(array $filters, ?array $classIds): LengthAwarePaginator;

    /**
     * @param  array<int, int>|null  $classIds  null = no class restriction
     */
    public function findStudent(int $studentId, ?array $classIds): ?Student;

    /**
     * @return array{total_records: int, strong_results: int, needs_attention: int}
     */
    public function getStudentSummary(int $studentId): array;

    public function getStudentRecords(int $studentId, array $filters): LengthAwarePaginator;

    public function createRecord(StoreStudentProgressDTO $dto): StudentProgress;
}
