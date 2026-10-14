<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\V1\Profile\ProfileResource;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $service) 
    {
        //
    }

    public function show(Request $request)
    {
        try {
            $profile = $this->service->getProfile($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully.',
                'data' => new ProfileResource($profile),
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

    public function update(UpdateProfileRequest $request)
    {
        try {
            $profile = $this->service->updateProfile($request->user(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new ProfileResource($profile),
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

