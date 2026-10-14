<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\System\StorePermissionRequest;
use App\Http\Requests\Api\V1\System\UpdatePermissionRequest;
use App\Http\Resources\Api\V1\System\PermissionResource;
use App\Services\System\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $service)
    {
        //
    }

    public function index(Request $request)
    {
        try {
            $permissions = $this->service->getAllPermissions($request->query());

            return response()->json([
            'success' => true,
            'message' => 'Permissions retrieved successfully.',
            'data' => PermissionResource::collection($permissions)->collection,
        ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = $this->service->createPermission(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'data' => new PermissionResource($permission),
            ], 201);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
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
            $permission = $this->service->getPermission($id);

            return response()->json([
                'success' => true,
                'message' => 'Permission retrieved successfully.',
                'data' => new PermissionResource($permission),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function update(UpdatePermissionRequest $request, int $id) 
    {
        try {
            $permission = $this->service->updatePermission(
                $id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'data' => new PermissionResource($permission),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
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
            $this->service->deletePermission($id);

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.',
                'data' => null,
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}
