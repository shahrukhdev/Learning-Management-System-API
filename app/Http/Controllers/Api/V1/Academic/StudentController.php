<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\DTOs\Academic\StoreStudentDTO;
use App\DTOs\Academic\UpdateStudentDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\StoreStudentRequest;
use App\Http\Requests\Api\V1\Academic\UpdateStudentRequest;
use App\Http\Resources\Api\V1\Academic\StudentResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Services\Academic\StudentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $students = $this->service->getAllStudents($request->query());

            return response()->json([
                'success' => true,
                'message' => 'Students retrieved successfully.',
                'data' => StudentResource::collection($students),
                'pagination' => new PaginationResource($students),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->service->createStudent(
                StoreStudentDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully.',
                'data' => new StudentResource($student),
            ], 201);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $student = $this->service->getStudent($id);

            return response()->json([
                'success' => true,
                'message' => 'Student retrieved successfully.',
                'data' => new StudentResource($student),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        try {
            $student = $this->service->updateStudent(
                $id,
                UpdateStudentDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully.',
                'data' => new StudentResource($student),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteStudent($id);

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully.',
                'data' => null,
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}
