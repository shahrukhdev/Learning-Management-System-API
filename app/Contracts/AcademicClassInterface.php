<?php

namespace App\Contracts;

use App\DTOs\Academic\StoreAcademicClassDTO;
use App\DTOs\Academic\UpdateAcademicClassDTO;
use App\Models\AcademicClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AcademicClassInterface
{
    public function getAllAcademicClasses(array $filters = []): LengthAwarePaginator;

    public function createAcademicClass(StoreAcademicClassDTO $dto): AcademicClass;

    public function getAcademicClass(int $id): AcademicClass;

    public function updateAcademicClass(int $id, UpdateAcademicClassDTO $dto): AcademicClass;

    public function deleteAcademicClass(int $id): void;
}