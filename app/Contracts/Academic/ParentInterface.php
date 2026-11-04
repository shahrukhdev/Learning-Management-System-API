<?php

namespace App\Contracts\Academic;

use App\DTOs\Academic\CreateParentDTO;
use App\DTOs\Academic\UpdateParentDTO;
use App\Models\StudentParent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ParentInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): StudentParent;

    public function findByUserId(int $userId): ?StudentParent;

    public function create(CreateParentDTO $dto): StudentParent;

    public function update(StudentParent $parent, UpdateParentDTO $dto): StudentParent;

    public function delete(StudentParent $parent): void;
}
