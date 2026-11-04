<?php

namespace App\Repositories\Academic;

use App\Contracts\Academic\ParentInterface;
use App\DTOs\Academic\CreateParentDTO;
use App\DTOs\Academic\UpdateParentDTO;
use App\Exceptions\ApiException;
use App\Models\StudentParent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ParentRepository implements ParentInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = StudentParent::query()
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

    public function findById(int $id): StudentParent
    {
        return StudentParent::query()
            ->with('user')
            ->find($id)
            ?? throw new ApiException(
                message: 'Parent not found.',
                status: 404
            );
    }

    public function findByUserId(int $userId): ?StudentParent
    {
        return StudentParent::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function create(CreateParentDTO $dto): StudentParent
    {
        return DB::transaction(function () use ($dto) {
            $parent = StudentParent::create($dto->toArray());

            return $parent->load('user');
        });
    }

    public function update(StudentParent $parent, UpdateParentDTO $dto): StudentParent
    {
        return DB::transaction(function () use ($parent, $dto) {
            $parent->update($dto->toArray());

            return $parent->load('user');
        });
    }

    public function delete(StudentParent $parent): void
    {
        $parent->delete();
    }
}
