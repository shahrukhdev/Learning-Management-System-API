
<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\DTOs\Dashboard\DashboardSummaryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\DashboardSummaryRequest;
use App\Http\Resources\Api\V1\Dashboard\DashboardSummaryResource;
use App\Services\Dashboard\DashboardSummaryService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardSummaryService $service
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
}
