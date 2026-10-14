<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTO\Auth\ForgotPasswordDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\ResetPasswordDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Resources\Api\V1\Auth\LoginResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service) 
    {
        //
    }

    public function login(LoginRequest $request)
    {
        try {
            $dto = LoginDTO::fromRequest($request);

            $result = $this->service->login($dto);

            return new LoginResource($result);

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

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $dto = ForgotPasswordDTO::fromRequest($request);

            $this->service->forgotPassword($dto);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent successfully.',
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
                'errors' => $exception->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $dto = ResetPasswordDTO::fromRequest($request);

            $this->service->resetPassword($dto);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
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

    public function logout(Request $request)
    {
        try {
            $this->service->logout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
        } catch (ApiException $e) {
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