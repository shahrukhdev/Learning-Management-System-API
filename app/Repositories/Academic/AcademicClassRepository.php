<?php

namespace App\Repositories\Academic;

use App\Contracts\AcademicClassInterface;
use App\DTOs\Academic\StoreAcademicClassDTO;
use App\DTOs\Academic\UpdateAcademicClassDTO;
use App\Exceptions\ApiException;
use App\Models\AcademicClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AcademicClassRepository implements AcademicClassInterface
{
    public function getAllAcademicClasses(array $filters = []): LengthAwarePaginator
    {
        $query = AcademicClass::query()->with('courses');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 10), 1), 100);

        return $query->latest()->paginate($perPage);
    }

    public function createAcademicClass(StoreAcademicClassDTO $dto): AcademicClass
    {
        return DB::transaction(function () use ($dto) {
            $class = AcademicClass::create($dto->toArray());

            $class->courses()->sync($dto->course_ids);

            return $class->load('courses');
        });
    }

    public function getAcademicClass(int $id): AcademicClass
    {
        $class = AcademicClass::with('courses')->find($id);

        if (!$class) {
            throw new ApiException(
                message: 'Academic class not found.',
                status: 404
            );
        }

        return $class;
    }

    public function updateAcademicClass(int $id, UpdateAcademicClassDTO $dto): AcademicClass
    {
        return DB::transaction(function () use ($id, $dto) {
            $class = AcademicClass::find($id);

            if (!$class) {
                throw new ApiException(
                    message: 'Academic class not found.',
                    status: 404
                );
            }

            $class->update($dto->toArray());

            if ($dto->course_ids !== null) {
                $class->courses()->sync($dto->course_ids);
            }

            return $class->load('courses');
        });
    }

    public function deleteAcademicClass(int $id): void
    {
        $class = AcademicClass::find($id);

        if (!$class) {
            throw new ApiException(
                message: 'Academic class not found.',
                status: 404
            );
        }

        $class->delete();
    }
}