<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\System\StoreRoleRequest;
use App\Http\Requests\Api\V1\System\UpdateRoleRequest;
use App\Http\Resources\Api\V1\System\RoleResource;
use App\Services\System\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service)
    {
        //
    }

    public function index(Request $request)
    {
        try {
            $roles = $this->service->getAllRoles($request->query());

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully.',
                'data' => RoleResource::collection($roles)->collection,
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

    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->service->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'data' => new RoleResource($role),
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
            $role = $this->service->getRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Role retrieved successfully.',
                'data' => new RoleResource($role),
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

    public function update(UpdateRoleRequest $request, int $id)
    {
        try {
            $role = $this->service->updateRole(
                $id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'data' => new RoleResource($role),
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
            $this->service->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
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
