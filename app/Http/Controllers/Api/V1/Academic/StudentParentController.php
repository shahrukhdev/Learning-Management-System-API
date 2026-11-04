<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\DTOs\Academic\CreateParentDTO;
use App\DTOs\Academic\UpdateParentDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\StoreParentRequest;
use App\Http\Requests\Api\V1\Academic\UpdateParentRequest;
use App\Http\Resources\Api\V1\Academic\ParentResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\Models\StudentParent;
use App\Services\Academic\ParentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentParentController extends Controller
{
    public function __construct(
        protected ParentService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $parents = $this->service->paginate($request->only([
                'search',
                'per_page',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Parents retrieved successfully.',
                'data' => ParentResource::collection($parents),
                'pagination' => new PaginationResource($parents),
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

    public function store(StoreParentRequest $request): JsonResponse
    {
        try {
            $parent = $this->service->create(
                CreateParentDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Parent created successfully.',
                'data' => new ParentResource($parent),
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

    public function show(int $id): JsonResponse
    {
        try {
            $parent = $this->service->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Parent retrieved successfully.',
                'data' => new ParentResource($parent),
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

    public function update(UpdateParentRequest $request, int $id): JsonResponse
    {
        try {
            $parent = $this->service->findById($id);

            $parent = $this->service->update(
                $parent,
                UpdateParentDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Parent updated successfully.',
                'data' => new ParentResource($parent),
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

    public function destroy(int $id): JsonResponse
    {
        try {
            $parent = $this->service->findById($id);

            $this->service->delete($parent);

            return response()->json([
                'success' => true,
                'message' => 'Parent deleted successfully.',
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
