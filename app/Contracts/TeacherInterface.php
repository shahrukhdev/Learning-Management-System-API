<?php

namespace App\Contracts;

use App\DTOs\Academic\CreateTeacherDTO;
use App\DTOs\Academic\UpdateTeacherDTO;
use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TeacherInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): Teacher;

    public function create(CreateTeacherDTO $dto): Teacher;

    public function update(Teacher $teacher, UpdateTeacherDTO $dto): Teacher;

    public function delete(Teacher $teacher): void;
}