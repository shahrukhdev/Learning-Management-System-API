
<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Hifz\HifzStatisticsResource;
use App\Services\Hifz\HifzStatisticsService;
use Exception;
use Illuminate\Http\JsonResponse;

class HifzStatisticsController extends Controller
{
    public function __construct(
        protected HifzStatisticsService $service
    ) {}

    public function show(int $studentId): JsonResponse
    {
        try {
            $statistics = $this->service->getStudentHifzStatistics($studentId);

            return response()->json([
                'success' => true,
                'message' => 'Hifz statistics retrieved successfully.',
                'data' => new HifzStatisticsResource($statistics),
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
