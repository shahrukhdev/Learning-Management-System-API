<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Hifz\JuzResource;
use App\Services\Hifz\JuzService;
use Exception;
use Illuminate\Http\JsonResponse;

class JuzController extends Controller
{
    public function __construct(
        protected JuzService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $juz = $this->service->getAllJuz();

            return response()->json([
                'success' => true,
                'message' => 'Juz retrieved successfully.',
                'data' => JuzResource::collection($juz),
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

    public function show(int $id): JsonResponse
    {
        try {
            $juz = $this->service->getJuz($id);

            return response()->json([
                'success' => true,
                'message' => 'Juz retrieved successfully.',
                'data' => new JuzResource($juz),
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