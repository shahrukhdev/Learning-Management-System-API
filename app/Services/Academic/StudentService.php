<?php

namespace App\Services\Academic;

use App\Contracts\StudentInterface;
use App\DTOs\Academic\StoreStudentDTO;
use App\DTOs\Academic\UpdateStudentDTO;
use App\Models\Student;
use App\Models\User;
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

    /**
     * Ensure the user has an active student record enrolled in the given class.
     * An existing record is kept (its phone is synced from the user's phone_no,
     * and its year and class are synced); otherwise a new one is created from
     * the user's data with the given year.
     */
    public function syncProfile(User $user, int $academicClassId, int $year): void
    {
        $student = $this->repository->findByUserId($user->id);

        if ($student) {
            if ($student->phone !== $user->phone_no || $student->year !== $year) {
                $student = $this->repository->updateStudent(
                    $student->id,
                    new UpdateStudentDTO(
                        user_id: null,
                        admission_number: null,
                        date_of_birth: null,
                        gender: null,
                        phone: $user->phone_no,
                        address: null,
                        userIdProvided: false,
                        admissionNumberProvided: false,
                        dateOfBirthProvided: false,
                        genderProvided: false,
                        phoneProvided: true,
                        addressProvided: false,
                        year: $year,
                        yearProvided: true,
                    )
                );
            }
        } else {
            $student = $this->repository->createStudent(
                new StoreStudentDTO(
                    user_id: $user->id,
                    admission_number: null,
                    date_of_birth: null,
                    gender: null,
                    phone: $user->phone_no,
                    address: null,
                    year: $year,
                )
            );
        }

        $this->repository->syncClass($student, $academicClassId);
    }

    /**
     * Soft-delete the user's active student record, if any.
     */
    public function deleteProfile(int $userId): void
    {
        $student = $this->repository->findByUserId($userId);

        if ($student) {
            $this->repository->deleteStudent($student->id);
        }
    }
}
