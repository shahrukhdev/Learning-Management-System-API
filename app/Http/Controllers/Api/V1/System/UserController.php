<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\System\StoreUserRequest;
use App\Http\Requests\Api\V1\System\UpdateUserRequest;
use App\Http\Resources\Api\V1\System\UserResource;
use App\Services\System\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        //
    }

    public function index(Request $request)
    {
        try {
            $users = $this->service->getAllUsers($request->query());

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully.',
                'data' => UserResource::collection($users)->collection,
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

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->service->createUser($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => new UserResource($user),
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
            $user = $this->service->getUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully.',
                'data' => new UserResource($user),
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

    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $user = $this->service->updateUser($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => new UserResource($user),
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
            $this->service->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
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