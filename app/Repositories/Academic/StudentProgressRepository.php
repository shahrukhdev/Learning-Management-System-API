<?php

namespace App\Repositories\Academic;

use App\Contracts\Academic\StudentProgressInterface;
use App\DTOs\Academic\StoreStudentProgressDTO;
use App\Enums\ProgressGrade;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class StudentProgressRepository implements StudentProgressInterface
{
    private const DEFAULT_PER_PAGE = 10;

    public function getTeacherClassIds(int $userId): array
    {
        $teacher = Teacher::query()
            ->where('user_id', $userId)
            ->first();

        if (!$teacher) {
            return [];
        }

        return $teacher->academicClasses()
            ->pluck('academic_classes.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function getStudents(array $filters, ?array $classIds): LengthAwarePaginator
    {
        $search = trim($filters['search'] ?? '');
        $classId = $filters['academic_class_id'] ?? null;

        return $this->visibleStudentsQuery($classIds)
            ->with(['user', 'academicClasses:id,name'])
            ->withCount('progressRecords')
            ->withMax('progressRecords', 'progress_date')
            ->when($classId, function (Builder $query) use ($classId) {
                $query->whereHas('academicClasses', function (Builder $query) use ($classId) {
                    $query->where('academic_classes.id', $classId);
                });
            })
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate($this->perPage($filters));
    }

    public function findStudent(int $studentId, ?array $classIds): ?Student
    {
        return $this->visibleStudentsQuery($classIds)
            ->with(['user', 'academicClasses.courses'])
            ->find($studentId);
    }

    public function getStudentSummary(int $studentId): array
    {
        $strong = ProgressGrade::strongValues();
        $attention = ProgressGrade::attentionValues();

        $row = StudentProgress::query()
            ->where('student_id', $studentId)
            ->toBase()
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw(
                'SUM(CASE WHEN grade IN (?, ?) THEN 1 ELSE 0 END) as strong_results',
                $strong
            )
            ->selectRaw(
                'SUM(CASE WHEN grade IN (?, ?) THEN 1 ELSE 0 END) as needs_attention',
                $attention
            )
            ->first();

        return [
            'total_records' => (int) ($row->total_records ?? 0),
            'strong_results' => (int) ($row->strong_results ?? 0),
            'needs_attention' => (int) ($row->needs_attention ?? 0),
        ];
    }

    public function getStudentRecords(int $studentId, array $filters): LengthAwarePaginator
    {
        $courseId = $filters['course_id'] ?? null;

        return StudentProgress::query()
            ->with(['course:id,name,code', 'recordedBy:id,name'])
            ->where('student_id', $studentId)
            ->when($courseId, fn (Builder $query) => $query->where('course_id', $courseId))
            ->orderByDesc('progress_date')
            ->orderByDesc('id')
            ->paginate($this->perPage($filters));
    }

    public function createRecord(StoreStudentProgressDTO $dto): StudentProgress
    {
        $record = StudentProgress::create($dto->toArray());

        return $record->load(['course:id,name,code', 'recordedBy:id,name']);
    }

    /**
     * Students query limited to the given classes (null = all students).
     */
    private function visibleStudentsQuery(?array $classIds): Builder
    {
        return Student::query()
            ->when($classIds !== null, function (Builder $query) use ($classIds) {
                $query->whereHas('academicClasses', function (Builder $query) use ($classIds) {
                    $query->whereIn('academic_classes.id', $classIds);
                });
            });
    }

    private function perPage(array $filters): int
    {
        return min(max((int) ($filters['per_page'] ?? self::DEFAULT_PER_PAGE), 1), 100);
    }
}
