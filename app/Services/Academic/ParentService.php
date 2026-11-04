<?php

namespace App\Services\Academic;

use App\Contracts\ParentInterface;
use App\DTOs\Academic\CreateParentDTO;
use App\DTOs\Academic\UpdateParentDTO;
use App\Models\StudentParent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParentService
{
    public function __construct(
        protected ParentInterface $parentRepository
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->parentRepository->paginate($filters);
    }

    public function findById(int $id): StudentParent
    {
        return $this->parentRepository->findById($id);
    }

    public function create(CreateParentDTO $dto): StudentParent
    {
        return $this->parentRepository->create($dto);
    }

    public function update(StudentParent $parent, UpdateParentDTO $dto): StudentParent
    {
        return $this->parentRepository->update($parent, $dto);
    }

    public function delete(StudentParent $parent): void
    {
        $this->parentRepository->delete($parent);
    }

    public function syncProfile(int $userId): void
    {
        $parent = $this->parentRepository->findByUserId($userId);

        if (!$parent) {
            $this->parentRepository->create(
                new CreateParentDTO(userId: $userId)
            );
        }
    }

    public function deleteProfile(int $userId): void
    {
        $parent = $this->parentRepository->findByUserId($userId);

        if ($parent) {
            $this->parentRepository->delete($parent);
        }
    }
}
