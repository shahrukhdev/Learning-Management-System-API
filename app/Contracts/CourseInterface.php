<?php

namespace App\Contracts;

use App\DTOs\Academic\StoreCourseDTO;
use App\DTOs\Academic\UpdateCourseDTO;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseInterface
{
    public function getAllCourses(array $filters = []): LengthAwarePaginator;

    public function createCourse(StoreCourseDTO $dto): Course;

    public function getCourse(int $id): Course;

    public function updateCourse(int $id, UpdateCourseDTO $dto): Course;

    public function deleteCourse(int $id): void;
}