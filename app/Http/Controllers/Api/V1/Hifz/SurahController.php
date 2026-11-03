<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Hifz\SurahResource;
use App\Services\Hifz\SurahService;
use Exception;
use Illuminate\Http\JsonResponse;

class SurahController extends Controller
{
    public function __construct(
        protected SurahService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $surahs = $this->service->getAllSurahs();

            return response()->json([
                'success' => true,
                'message' => 'Surahs retrieved successfully.',
                'data' => SurahResource::collection($surahs),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while retrieving Surahs.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $surah = $this->service->getSurah($id);

            return response()->json([
                'success' => true,
                'message' => 'Surah retrieved successfully.',
                'data' => new SurahResource($surah),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while retrieving the Surah.',
            ], 500);
        }
    }
}