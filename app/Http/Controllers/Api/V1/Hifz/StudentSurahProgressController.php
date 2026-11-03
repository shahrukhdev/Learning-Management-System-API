<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\DTOs\Hifz\StoreStudentSurahProgressDTO;
use App\DTOs\Hifz\UpdateStudentSurahProgressDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Hifz\StoreStudentSurahProgressRequest;
use App\Http\Requests\Api\V1\Hifz\UpdateStudentSurahProgressRequest;
use App\Http\Resources\Api\V1\Hifz\StudentSurahProgressResource;
use App\Services\Hifz\StudentSurahProgressService;
use Exception;
use Illuminate\Http\JsonResponse;

class StudentSurahProgressController extends Controller
{
    public function __construct(
        protected StudentSurahProgressService $service
    ) {}

    public function index(int $studentId): JsonResponse
    {
        try {
            $progress = $this->service->getStudentProgress($studentId);

            return response()->json([
                'success' => true,
                'message' => 'Student Surah progress retrieved successfully.',
                'data' => StudentSurahProgressResource::collection($progress),
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

    public function store(
        StoreStudentSurahProgressRequest $request,
        int $studentId
    ): JsonResponse {
        try {
            $progress = $this->service->createProgress(
                StoreStudentSurahProgressDTO::fromRequest($request, $studentId)
            );

            return response()->json([
                'success' => true,
                'message' => 'Surah progress created successfully.',
                'data' => new StudentSurahProgressResource($progress),
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

    public function update(
        UpdateStudentSurahProgressRequest $request,
        int $id
    ): JsonResponse {
        try {
            $progress = $this->service->updateProgress(
                $id,
                UpdateStudentSurahProgressDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Surah progress updated successfully.',
                'data' => new StudentSurahProgressResource($progress),
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
            $this->service->deleteProgress($id);

            return response()->json([
                'success' => true,
                'message' => 'Surah progress deleted successfully.',
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