<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Hifz\GetMyHifzSessionsRequest;
use App\Http\Resources\Api\V1\Hifz\HifzSessionResource;
use App\Http\Resources\Api\V1\Hifz\HifzStatisticsResource;
use App\Http\Resources\Api\V1\Hifz\QuranMapSurahResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Services\Hifz\MyHifzService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Read-only Hifz endpoints for the signed-in student (Student role only).
 */
class MyHifzController extends Controller
{
    public function __construct(
        protected MyHifzService $service
    ) {}

    /**
     * Profile + statistics + Quran map in one call.
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $hifz = $this->service->getMyHifz($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Hifz retrieved successfully.',
                'data' => [
                    'student' => $hifz['student'],
                    'statistics' => new HifzStatisticsResource($hifz['statistics']),
                    'quran_map' => [
                        'statistics' => $hifz['quran_map_statistics'],
                        'surahs' => QuranMapSurahResource::collection($hifz['quran_map_surahs']),
                    ],
                ],
            ]);
        } catch (ApiException $exception) {
            return $this->errorResponse($exception);
        } catch (Exception $exception) {
            return $this->serverErrorResponse();
        }
    }

    /**
     * The student's Hifz sessions, newest first (10 per page by default).
     */
    public function sessions(GetMyHifzSessionsRequest $request): JsonResponse
    {
        try {
            $perPage = (int) ($request->validated('per_page') ?? 10);

            $sessions = $this->service->getMySessions(
                $request->user(),
                $request->validated('date'),
                $perPage,
                (int) ($request->validated('page') ?? 1)
            );

            return response()->json([
                'success' => true,
                'message' => 'Hifz sessions retrieved successfully.',
                'data' => HifzSessionResource::collection($sessions)->collection,
                'pagination' => (new PaginationResource($sessions))->resolve(),
            ]);
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
