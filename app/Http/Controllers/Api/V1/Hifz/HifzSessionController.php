<?php

namespace App\Http\Controllers\Api\V1\Hifz;

use App\DTOs\Hifz\StoreHifzSessionDTO;
use App\DTOs\Hifz\UpdateHifzSessionDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Hifz\GetStudentHifzSessionsRequest;
use App\Http\Requests\Api\V1\Hifz\StoreHifzSessionRequest;
use App\Http\Requests\Api\V1\Hifz\UpdateHifzSessionRequest;
use App\Http\Resources\Api\V1\Hifz\HifzSessionResource;
use App\Services\Hifz\HifzSessionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HifzSessionController extends Controller
{
    public function __construct(
        protected HifzSessionService $service
    ) {}

    public function index(
        GetStudentHifzSessionsRequest $request,
        int $studentId
    ): JsonResponse {
        try {
            $sessions = $this->service->getStudentSessions(
                $studentId,
                $request->date
            );

            return response()->json([
                'success' => true,
                'message' => 'Hifz sessions retrieved successfully.',
                'data' => HifzSessionResource::collection($sessions),
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
        StoreHifzSessionRequest $request,
        int $studentId
    ): JsonResponse {
        try {
            $user = Auth::user();
            $teacher = $user?->teacher;

            if (! $teacher) {
                throw new ApiException(
                    message: 'Authenticated user is not a teacher.',
                    status: 403
                );
            }

            $dto = StoreHifzSessionDTO::fromRequest(
                $request,
                $studentId,
                $teacher->id
            );

            $session = $this->service->createSession($dto);

            return response()->json([
                'success' => true,
                'message' => 'Hifz session created successfully.',
                'data' => new HifzSessionResource($session),
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

    public function show(int $studentId, int $id): JsonResponse
    {
        try {
            $session = $this->service->getSession($id, $studentId);

            return response()->json([
                'success' => true,
                'message' => 'Hifz session retrieved successfully.',
                'data' => new HifzSessionResource($session),
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

    public function update(
        UpdateHifzSessionRequest $request,
        int $studentId,
        int $id
    ): JsonResponse {
        try {
            $dto = UpdateHifzSessionDTO::fromRequest($request);

            $session = $this->service->updateSession(
                $id,
                $studentId,
                $dto
            );

            return response()->json([
                'success' => true,
                'message' => 'Hifz session updated successfully.',
                'data' => new HifzSessionResource($session),
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

    public function destroy(int $studentId, int $id): JsonResponse
    {
        try {
            $this->service->deleteSession($id, $studentId);

            return response()->json([
                'success' => true,
                'message' => 'Hifz session deleted successfully.',
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
