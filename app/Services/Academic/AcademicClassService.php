<?php

namespace App\Services\Academic;

use App\Contracts\AcademicClassInterface;
use App\DTOs\Academic\StoreAcademicClassDTO;
use App\DTOs\Academic\UpdateAcademicClassDTO;
use App\Models\AcademicClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AcademicClassService
{
    public function __construct(protected AcademicClassInterface $repository)
    {
    }

    public function getAllAcademicClasses(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllAcademicClasses($filters);
    }

    public function createAcademicClass(StoreAcademicClassDTO $dto): AcademicClass
    {
        return $this->repository->createAcademicClass($dto);
    }

    public function getAcademicClass(int $id): AcademicClass
    {
        return $this->repository->getAcademicClass($id);
    }

    public function updateAcademicClass(int $id, UpdateAcademicClassDTO $dto): AcademicClass
    {
        return $this->repository->updateAcademicClass($id, $dto);
    }

    public function deleteAcademicClass(int $id): void
    {
        $this->repository->deleteAcademicClass($id);
    }
}