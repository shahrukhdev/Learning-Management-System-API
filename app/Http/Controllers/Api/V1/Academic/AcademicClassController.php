<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\StoreAcademicClassRequest;
use App\Http\Requests\Api\V1\Academic\UpdateAcademicClassRequest;
use App\Http\Resources\Api\V1\Academic\AcademicClassResource;
use App\Http\Resources\Api\V1\PaginationResource;
use App\DTOs\Academic\StoreAcademicClassDTO;
use App\DTOs\Academic\UpdateAcademicClassDTO;
use App\Services\Academic\AcademicClassService;
use Illuminate\Http\Request;
use Exception;

class AcademicClassController extends Controller
{
    public function __construct(protected AcademicClassService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $classes = $this->service->getAllAcademicClasses($request->query());

            return response()->json([
                'success' => true,
                'message' => 'Academic classes retrieved successfully.',
                'data' => AcademicClassResource::collection($classes)->collection,
                'pagination' => (new PaginationResource($classes))->resolve(),
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatus());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function store(StoreAcademicClassRequest $request)
    {
        try {
            $class = $this->service->createAcademicClass(
                StoreAcademicClassDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Academic class created successfully.',
                'data' => new AcademicClassResource($class),
            ], 201);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatus());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $class = $this->service->getAcademicClass($id);

            return response()->json([
                'success' => true,
                'message' => 'Academic class retrieved successfully.',
                'data' => new AcademicClassResource($class),
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatus());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function update(UpdateAcademicClassRequest $request, int $id)
    {
        try {
            $class = $this->service->updateAcademicClass(
                $id,
                UpdateAcademicClassDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Academic class updated successfully.',
                'data' => new AcademicClassResource($class),
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatus());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->deleteAcademicClass($id);

            return response()->json([
                'success' => true,
                'message' => 'Academic class deleted successfully.',
                'data' => null,
            ]);
        } catch (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatus());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}