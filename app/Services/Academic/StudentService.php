
<?php

namespace App\Services\Academic;

use App\Contracts\StudentInterface;
use App\DTOs\Academic\StoreStudentDTO;
use App\DTOs\Academic\UpdateStudentDTO;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentService
{
    public function __construct(
        protected StudentInterface $repository
    ) {}

    public function getAllStudents(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllStudents($filters);
    }

    public function createStudent(StoreStudentDTO $dto): Student
    {
        return $this->repository->createStudent($dto);
    }

    public function getStudent(int $id): Student
    {
        return $this->repository->getStudent($id);
    }

    public function updateStudent(int $id, UpdateStudentDTO $dto): Student
    {
        return $this->repository->updateStudent($id, $dto);
    }

    public function deleteStudent(int $id): void
    {
        $this->repository->deleteStudent($id);
    }
}
