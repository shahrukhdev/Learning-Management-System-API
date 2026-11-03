<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\StoreCourseRequest;
use App\Http\Requests\Api\V1\Academic\UpdateCourseRequest;
use App\Http\Resources\Api\V1\Academic\CourseResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Services\Academic\CourseService;
use App\DTOs\Academic\StoreCourseDTO;
use App\DTOs\Academic\UpdateCourseDTO;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(protected CourseService $service)
    {
        //
    }

    public function index(Request $request)
    {
        try {
            $courses = $this->service->getAllCourses($request->query());

            return response()->json([
                'success' => true,
                'message' => 'Courses retrieved successfully.',
                'data' => CourseResource::collection($courses)->collection,
                'pagination' => (new PaginationResource($courses))->resolve(),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $course = $this->service->createCourse(
                StoreCourseDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully.',
                'data' => new CourseResource($course),
            ], 201);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $course = $this->service->getCourse($id);

            return response()->json([
                'success' => true,
                'message' => 'Course retrieved successfully.',
                'data' => new CourseResource($course),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function update(UpdateCourseRequest $request, int $id)
    {
        try {
            $course = $this->service->updateCourse(
                $id,
                UpdateCourseDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully.',
                'data' => new CourseResource($course),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->deleteCourse($id);

            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully.',
                'data' => null,
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}