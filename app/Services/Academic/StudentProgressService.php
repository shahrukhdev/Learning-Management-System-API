<?php

namespace App\Services\Academic;

use App\Contracts\Academic\StudentProgressInterface;
use App\Contracts\StudentInterface;
use App\DTOs\Academic\StoreStudentProgressDTO;
use App\DTOs\Academic\UpdateStudentProgressDTO;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class StudentProgressService
{
    /** Roles that can view and log progress for every student. */
    private const FULL_ACCESS_ROLES = ['Super Admin', 'Admin'];

    /** Role that can view and log progress only for students in their assigned classes. */
    private const TEACHER_ROLE = 'Teacher';

    /** Role that can view only their own progress (the /my endpoints). */
    private const STUDENT_ROLE = 'Student';

    public function __construct(
        protected StudentProgressInterface $repository,
        protected StudentInterface $studentRepository,
    ) {}

    /**
     * The signed-in student's own profile, summary and subjects (Student role only).
     * A student without a student record gets student = null, zero counts and no courses.
     *
     * @return array{student: ?Student, summary: array, courses: Collection}
     */
    public function getMyDetails(User $user): array
    {
        $student = $this->resolveMyStudent($user);

        if (!$student) {
            return [
                'student' => null,
                'summary' => ['total_records' => 0, 'strong_results' => 0, 'needs_attention' => 0],
                'courses' => collect(),
            ];
        }

        return [
            'student' => $student,
            'summary' => $this->repository->getStudentSummary($student->id),
            'courses' => $this->studentCourses($student),
        ];
    }

    /**
     * The signed-in student's own records, newest first (Student role only).
     */
    public function getMyRecords(User $user, array $filters = []): LengthAwarePaginator
    {
        $student = $this->resolveMyStudent($user);

        if (!$student) {
            return new Paginator(
                [],
                0,
                (int) ($filters['per_page'] ?? 10),
                (int) ($filters['page'] ?? 1)
            );
        }

        return $this->repository->getStudentRecords($student->id, $filters);
    }

    private function resolveMyStudent(User $user): ?Student
    {
        if (!$user->hasRole(self::STUDENT_ROLE)) {
            throw new ApiException(
                message: 'Only students can view their own progress.',
                status: 403
            );
        }

        $student = $this->studentRepository->findByUserId($user->id);

        // Same query as the Admin details page (classes + courses + stats).
        return $student ? $this->repository->findStudent($student->id, null) : null;
    }

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

        $this->ensureCourseBelongsToStudent($student, $dto->course_id);

        return $this->repository->createRecord($dto);
    }

    public function getRecord(User $user, int $studentId, int $id): StudentProgress
    {
        $student = $this->getAccessibleStudent($user, $studentId);

        return $this->findStudentRecord($student, $id);
    }

    public function updateRecord(
        User $user,
        int $studentId,
        int $id,
        UpdateStudentProgressDTO $dto
    ): StudentProgress {
        $student = $this->getAccessibleStudent($user, $studentId);
        $record = $this->findStudentRecord($student, $id);

        if ($dto->course_id !== null && $dto->course_id !== $record->course_id) {
            $this->ensureCourseBelongsToStudent($student, $dto->course_id);
        }

        return $this->repository->updateRecord($record, $dto);
    }

    public function deleteRecord(User $user, int $studentId, int $id): void
    {
        $student = $this->getAccessibleStudent($user, $studentId);
        $record = $this->findStudentRecord($student, $id);

        $this->repository->deleteRecord($record);
    }

    private function findStudentRecord(Student $student, int $id): StudentProgress
    {
        $record = $this->repository->findRecord($id, $student->id);

        if (!$record) {
            throw new ApiException(
                message: 'Progress record not found.',
                status: 404
            );
        }

        return $record;
    }

    private function ensureCourseBelongsToStudent(Student $student, int $courseId): void
    {
        if ($this->studentCourses($student)->contains('id', $courseId)) {
            return;
        }

        throw new ApiException(
            message: 'The selected subject is not assigned to this student\'s class.',
            status: 422,
            errors: [
                'course_id' => ['The selected subject is not assigned to this student\'s class.'],
            ],
        );
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
