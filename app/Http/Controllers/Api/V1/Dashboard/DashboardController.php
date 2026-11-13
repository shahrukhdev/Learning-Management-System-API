<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\DTOs\Dashboard\DashboardSummaryDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\DashboardSummaryRequest;
use App\Http\Resources\Api\V1\Dashboard\DashboardSummaryResource;
use App\Services\Dashboard\DashboardSummaryService;
use App\Services\Dashboard\StudentDashboardService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardSummaryService $service,
        protected StudentDashboardService $studentDashboardService,
    ) {}

    public function summary(DashboardSummaryRequest $request): JsonResponse
    {
        try {
            $dto = DashboardSummaryDTO::fromRequest($request);

            $summary = $this->service->getDashboardSummary($dto);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard summary retrieved successfully.',
                'data' => new DashboardSummaryResource($summary),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Dashboard for the signed-in student (Student role only).
     */
    public function studentSummary(DashboardSummaryRequest $request): JsonResponse
    {
        try {
            $summary = $this->studentDashboardService->getStudentSummary(
                $request->user(),
                DashboardSummaryDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Student dashboard retrieved successfully.',
                'data' => new DashboardSummaryResource($summary),
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
