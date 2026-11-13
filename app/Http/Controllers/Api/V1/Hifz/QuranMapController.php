<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Hifz\QuranMapSurahResource;
use App\Services\Hifz\QuranMapService;
use Exception;
use Illuminate\Http\JsonResponse;

class QuranMapController extends Controller
{
    public function __construct(
        protected QuranMapService $service
    ) {}

    public function index(int $studentId): JsonResponse
    {
        try {
            $surahs = $this->service->getStudentQuranMap($studentId);

            return response()->json([
                'success' => true,
                'message' => 'Quran map retrieved successfully.',
                'data' => [
                    'statistics' => $this->service->summarise($surahs),
                    'surahs' => QuranMapSurahResource::collection($surahs),
                ],
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}