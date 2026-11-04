<?php

namespace App\Services\Academic;

use App\Contracts\TeacherInterface;
use App\DTOs\Academic\CreateTeacherDTO;
use App\DTOs\Academic\UpdateTeacherDTO;
use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeacherService
{
    public function __construct(
        protected TeacherInterface $teacherRepository
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->teacherRepository->paginate($filters);
    }

    public function findById(int $id): Teacher
    {
        return $this->teacherRepository->findById($id);
    }

    public function create(CreateTeacherDTO $dto): Teacher
    {
        return $this->teacherRepository->create($dto);
    }

    public function update(Teacher $teacher, UpdateTeacherDTO $dto): Teacher
    {
        return $this->teacherRepository->update($teacher, $dto);
    }

    public function delete(Teacher $teacher): void
    {
        $this->teacherRepository->delete($teacher);
    }

    public function syncProfile(int $userId): void
    {
        $teacher = $this->teacherRepository->findByUserId($userId);

        if (!$teacher) {
            $this->teacherRepository->create(
                new CreateTeacherDTO(userId: $userId)
            );
        }
    }

    public function deleteProfile(int $userId): void
    {
        $teacher = $this->teacherRepository->findByUserId($userId);

        if ($teacher) {
            $this->teacherRepository->delete($teacher);
        }
    }
}