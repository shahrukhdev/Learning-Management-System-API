<?php

namespace App\Services\Academic;

use App\Contracts\Academic\StudentProgressInterface;
use App\DTOs\Academic\StoreStudentProgressDTO;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StudentProgressService
{
    /** Roles that can view and log progress for every student. */
    private const FULL_ACCESS_ROLES = ['Super Admin', 'Admin'];

    /** Role that can view and log progress only for students in their assigned classes. */
    private const TEACHER_ROLE = 'Teacher';

    public function __construct(
        protected StudentProgressInterface $repository
    ) {}

    public function getStudents(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getStudents(
            $filters,
            $this->accessibleClassIds($user)
        );
    }

    /**
     * Student profile, progress summary and the subjects (courses) of the student's class.
     *
     * @return array{student: Student, summary: array, courses: Collection}
     */
    public function getStudentDetails(User $user, int $studentId): array
    {
        $student = $this->getAccessibleStudent($user, $studentId);

        return [
            'student' => $student,
            'summary' => $this->repository->getStudentSummary($student->id),
            'courses' => $this->studentCourses($student),
        ];
    }

    public function getStudentRecords(User $user, int $studentId, array $filters = []): LengthAwarePaginator
    {
        $student = $this->getAccessibleStudent($user, $studentId);

        return $this->repository->getStudentRecords($student->id, $filters);
    }

    public function logProgress(User $user, StoreStudentProgressDTO $dto): StudentProgress
    {
        $student = $this->getAccessibleStudent($user, $dto->student_id);

        if (!$this->studentCourses($student)->contains('id', $dto->course_id)) {
            throw new ApiException(
                message: 'The selected subject is not assigned to this student\'s class.',
                status: 422,
                errors: [
                    'course_id' => ['The selected subject is not assigned to this student\'s class.'],
                ],
            );
        }

        return $this->repository->createRecord($dto);
    }

    /**
     * Class IDs the user may access: null = all classes.
     */
    private function accessibleClassIds(User $user): ?array
    {
        if ($user->hasAnyRole(self::FULL_ACCESS_ROLES)) {
            return null;
        }

        if ($user->hasRole(self::TEACHER_ROLE)) {
            return $this->repository->getTeacherClassIds($user->id);
        }

        throw new ApiException(
            message: 'You are not allowed to access student progress.',
            status: 403
        );
    }

    private function getAccessibleStudent(User $user, int $studentId): Student
    {
        $student = $this->repository->findStudent(
            $studentId,
            $this->accessibleClassIds($user)
        );

        if (!$student) {
            throw new ApiException(
                message: 'Student not found.',
                status: 404
            );
        }

        return $student;
    }

    /**
     * Unique courses across the student's classes.
     */
    private function studentCourses(Student $student): Collection
    {
        return $student->academicClasses
            ->flatMap(fn ($class) => $class->courses)
            ->unique('id')
            ->sortBy('name')
            ->values();
    }
}
