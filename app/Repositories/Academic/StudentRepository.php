<?php

namespace App\Repositories\Academic;

use App\Contracts\StudentInterface;
use App\DTOs\Academic\StoreStudentDTO;
use App\DTOs\Academic\UpdateStudentDTO;
use App\Exceptions\ApiException;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StudentRepository implements StudentInterface
{
    public function getAllStudents(array $filters = []): LengthAwarePaginator
    {
        $search = trim($filters['search'] ?? '');

        return Student::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(
                min(max((int) ($filters['per_page'] ?? 15), 1), 100)
            );
    }

    public function createStudent(StoreStudentDTO $dto): Student
    {
        return DB::transaction(function () use ($dto) {
            $student = Student::create($dto->toArray());

            return $student->load('user');
        });
    }

    public function getStudent(int $id): Student
    {
        $student = Student::query()
            ->with('user')
            ->find($id);

        if (!$student) {
            throw new ApiException(
                message: 'Student not found.',
                status: 404
            );
        }

        return $student;
    }

    public function updateStudent(int $id, UpdateStudentDTO $dto): Student
    {
        return DB::transaction(function () use ($id, $dto) {
            $student = Student::query()->find($id);

            if (!$student) {
                throw new ApiException(
                    message: 'Student not found.',
                    status: 404
                );
            }

            $student->update($dto->toArray());

            return $student->load('user');
        });
    }

    public function deleteStudent(int $id): void
    {
        $student = Student::query()->find($id);

        if (!$student) {
            throw new ApiException(
                message: 'Student not found.',
                status: 404
            );
        }

        $student->delete();
    }

    public function findByUserId(int $userId): ?Student
    {
        return Student::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function syncClass(Student $student, int $academicClassId): void
    {
        $student->academicClasses()->sync([$academicClassId]);
    }
}
