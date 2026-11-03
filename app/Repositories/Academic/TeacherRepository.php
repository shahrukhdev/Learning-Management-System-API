<?php

namespace App\Repositories\Academic;

use App\Contracts\TeacherInterface;
use App\DTOs\Academic\CreateTeacherDTO;
use App\DTOs\Academic\UpdateTeacherDTO;
use App\Exceptions\ApiException;
use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeacherRepository implements TeacherInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Teacher::query()
            ->with('user');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): Teacher
    {
        return Teacher::query()
            ->with('user')
            ->find($id)
            ?? throw new ApiException(
                message: 'Teacher not found.',
                status: 404
            );
    }

    public function create(CreateTeacherDTO $dto): Teacher
    {
        return DB::transaction(function () use ($dto) {
            $teacher = Teacher::create($dto->toArray());

            return $teacher->load('user');
        });
    }

    public function update(Teacher $teacher, UpdateTeacherDTO $dto): Teacher
    {
        return DB::transaction(function () use ($teacher, $dto) {
            $teacher->update($dto->toArray());

            return $teacher->load('user');
        });
    }

    public function delete(Teacher $teacher): void
    {
        $teacher->delete();
    }
}