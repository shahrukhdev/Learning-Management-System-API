<?php

namespace App\Http\Controllers\Api\V1\Notice;

use App\DTOs\Notice\StoreNoticeDTO;
use App\DTOs\Notice\UpdateNoticeDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notice\StoreNoticeRequest;
use App\Http\Requests\Api\V1\Notice\UpdateNoticeRequest;
use App\Http\Resources\Api\V1\Notice\NoticeResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Services\Notice\NoticeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function __construct(
        protected NoticeService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $notices = $this->service->getAllNotices($request->query(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Notices retrieved successfully.',
                'data' => NoticeResource::collection($notices),
                'pagination' => new PaginationResource($notices),
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

    public function store(StoreNoticeRequest $request): JsonResponse
    {
        try {
            $notice = $this->service->createNotice(
                StoreNoticeDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Notice created successfully.',
                'data' => new NoticeResource($notice),
            ], 201);
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

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $notice = $this->service->getNotice($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Notice retrieved successfully.',
                'data' => new NoticeResource($notice),
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

    public function update(UpdateNoticeRequest $request, int $id): JsonResponse
    {
        try {
            $notice = $this->service->updateNotice(
                $id,
                UpdateNoticeDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Notice updated successfully.',
                'data' => new NoticeResource($notice),
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

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteNotice($id);

            return response()->json([
                'success' => true,
                'message' => 'Notice deleted successfully.',
                'data' => null,
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
