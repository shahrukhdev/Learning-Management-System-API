<?php

namespace App\Repositories\Academic;

use App\Contracts\CourseInterface;
use App\DTOs\Academic\StoreCourseDTO;
use App\DTOs\Academic\UpdateCourseDTO;
use App\Exceptions\ApiException;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CourseRepository implements CourseInterface
{
    public function getAllCourses(array $filters = []): LengthAwarePaginator
    {
        $query = Course::query()
            ->with('academicClasses');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 10), 1), 100);

        return $query->latest()->paginate($perPage);
    }

    public function createCourse(StoreCourseDTO $dto): Course
    {
        return DB::transaction(function () use ($dto) {
            $course = Course::create($dto->toArray());

            $course->academicClasses()->sync($dto->academic_class_ids);

            return $course->load('academicClasses');
        });
    }

    public function getCourse(int $id): Course
    {
        $course = Course::with('academicClasses')->find($id);

        if (!$course) {
            throw new ApiException(
                message: 'Course not found.',
                status: 404
            );
        }

        return $course;
    }

    public function updateCourse(int $id, UpdateCourseDTO $dto): Course
    {
        return DB::transaction(function () use ($id, $dto) {
            $course = Course::find($id);

            if (!$course) {
                throw new ApiException(
                    message: 'Course not found.',
                    status: 404
                );
            }

            $course->update($dto->toArray());

            if ($dto->academic_class_ids !== null) {
                $course->academicClasses()->sync($dto->academic_class_ids);
            }

            return $course->load('academicClasses');
        });
    }

    public function deleteCourse(int $id): void
    {
        $course = Course::find($id);

        if (!$course) {
            throw new ApiException(
                message: 'Course not found.',
                status: 404
            );
        }

        $course->delete();
    }
}