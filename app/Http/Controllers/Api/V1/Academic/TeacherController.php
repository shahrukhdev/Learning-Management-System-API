<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\DTOs\Academic\CreateTeacherDTO;
use App\DTOs\Academic\UpdateTeacherDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\StoreTeacherRequest;
use App\Http\Requests\Api\V1\Academic\UpdateTeacherRequest;
use App\Http\Resources\Api\V1\Academic\TeacherResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Models\Teacher;
use App\Services\Academic\TeacherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $teachers = $this->service->paginate($request->only([
                'search',
                'per_page',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Teachers retrieved successfully.',
                'data' => TeacherResource::collection($teachers),
                'pagination' => new PaginationResource($teachers),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->status);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        try {
            $teacher = $this->service->create(
                CreateTeacherDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully.',
                'data' => new TeacherResource($teacher),
            ], 201);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->status);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $teacher = $this->service->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Teacher retrieved successfully.',
                'data' => new TeacherResource($teacher),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->status);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function update(UpdateTeacherRequest $request, int $id): JsonResponse
    {
        try {
            $teacher = $this->service->findById($id);

            $teacher = $this->service->update(
                $teacher,
                UpdateTeacherDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Teacher updated successfully.',
                'data' => new TeacherResource($teacher),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->status);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $teacher = $this->service->findById($id);

            $this->service->delete($teacher);

            return response()->json([
                'success' => true,
                'message' => 'Teacher deleted successfully.',
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->status);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}