<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\DTOs\Academic\StoreStudentProgressDTO;
use App\Enums\ProgressGrade;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\GetProgressStudentsRequest;
use App\Http\Requests\Api\V1\Academic\GetStudentProgressRecordsRequest;
use App\Http\Requests\Api\V1\Academic\StoreStudentProgressRequest;
use App\Http\Resources\Api\V1\Academic\ProgressStudentResource;
use App\Http\Resources\Api\V1\Academic\StudentProgressResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Services\Academic\StudentProgressService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProgressController extends Controller
{
    public function __construct(
        protected StudentProgressService $service
    ) {}

    /**
     * Students list (10 per page): all students for Super Admin / Admin,
     * only students of their assigned classes for Teachers.
     */
    public function index(GetProgressStudentsRequest $request): JsonResponse
    {
        try {
            $students = $this->service->getStudents(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Students retrieved successfully.',
                'data' => ProgressStudentResource::collection($students)->collection,
                'pagination' => (new PaginationResource($students))->resolve(),
            ]);
        } catch (ApiException $exception) {
            return $this->errorResponse($exception);
        } catch (Exception $exception) {
            return $this->serverErrorResponse();
        }
    }

    /**
     * Student profile, summary cards and subject (course) filters.
     */
    public function show(Request $request, int $studentId): JsonResponse
    {
        try {
            $details = $this->service->getStudentDetails($request->user(), $studentId);

            return response()->json([
                'success' => true,
                'message' => 'Student progress retrieved successfully.',
                'data' => [
                    'student' => new ProgressStudentResource($details['student']),
                    'summary' => $details['summary'],
                    'courses' => $details['courses']->map(fn ($course) => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'code' => $course->code ?? '',
                    ])->values(),
                    'grades' => array_map(
                        fn (ProgressGrade $grade) => $grade->value,
                        ProgressGrade::cases()
                    ),
                ],
            ]);
        } catch (ApiException $exception) {
            return $this->errorResponse($exception);
        } catch (Exception $exception) {
            return $this->serverErrorResponse();
        }
    }

    /**
     * Progress history (10 per page, latest first), optionally filtered by course.
     */
    public function records(GetStudentProgressRecordsRequest $request, int $studentId): JsonResponse
    {
        try {
            $records = $this->service->getStudentRecords(
                $request->user(),
                $studentId,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Progress records retrieved successfully.',
                'data' => StudentProgressResource::collection($records)->collection,
                'pagination' => (new PaginationResource($records))->resolve(),
            ]);
        } catch (ApiException $exception) {
            return $this->errorResponse($exception);
        } catch (Exception $exception) {
            return $this->serverErrorResponse();
        }
    }

    /**
     * Log a progress record for a student.
     */
    public function store(StoreStudentProgressRequest $request, int $studentId): JsonResponse
    {
        try {
            $user = $request->user();

            $record = $this->service->logProgress(
                $user,
                StoreStudentProgressDTO::fromRequest($request, $studentId, $user->id)
            );

            return response()->json([
                'success' => true,
                'message' => 'Progress logged successfully.',
                'data' => new StudentProgressResource($record),
            ], 201);
        } catch (ApiException $exception) {
            return $this->errorResponse($exception);
        } catch (Exception $exception) {
            return $this->serverErrorResponse();
        }
    }

    private function errorResponse(ApiException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'errors' => $exception->getErrors(),
        ], $exception->getStatus());
    }

    private function serverErrorResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'errors' => [],
        ], 500);
    }
}
