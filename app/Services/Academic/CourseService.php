<?php

namespace App\Services\Academic;

use App\Contracts\CourseInterface;
use App\DTOs\Academic\StoreCourseDTO;
use App\DTOs\Academic\UpdateCourseDTO;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function __construct(protected CourseInterface $repository)
    {
        //
    }

    public function getAllCourses(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllCourses($filters);
    }

    public function createCourse(StoreCourseDTO $dto): Course
    {
        return $this->repository->createCourse($dto);
    }

    public function getCourse(int $id): Course
    {
        return $this->repository->getCourse($id);
    }

    public function updateCourse(int $id, UpdateCourseDTO $dto): Course
    {
        return $this->repository->updateCourse($id, $dto);
    }

    public function deleteCourse(int $id): void
    {
        $this->repository->deleteCourse($id);
    }
}