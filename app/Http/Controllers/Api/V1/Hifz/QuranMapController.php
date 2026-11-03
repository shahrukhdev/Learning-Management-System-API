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

            $memorised = $surahs->filter(
                fn ($surah) => $surah->studentProgress->first()?->status === 'memorised'
            )->count();

            $inProgress = $surahs->filter(
                fn ($surah) => $surah->studentProgress->first()?->status === 'progress'
            )->count();

            $remaining = $surahs->count() - $memorised - $inProgress;

            return response()->json([
                'success' => true,
                'message' => 'Quran map retrieved successfully.',
                'data' => [
                    'statistics' => [
                        'total_surahs' => $surahs->count(),
                        'memorised' => $memorised,
                        'in_progress' => $inProgress,
                        'remaining' => $remaining,
                    ],
                    'surahs' => QuranMapSurahResource::collection($surahs),
                ],
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