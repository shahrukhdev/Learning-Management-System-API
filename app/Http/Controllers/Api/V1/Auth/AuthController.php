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

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) 
    {
        //
    }

    public function login(LoginRequest $request)
    {
        try {
            $dto = LoginDTO::fromRequest($request);

            $user = $this->authService->login($dto);

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => new LoginResource($user),
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

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $dto = ForgotPasswordDTO::fromRequest($request);

            $this->authService->forgotPassword($dto);

            return response()->json([
                'success' => true,
                'message' => 'If an account exists for this email, a password reset link has been sent.',
                'data' => null,
            ]);

        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());

        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $dto = ResetPasswordDTO::fromRequest($request);

            $this->authService->resetPassword($dto);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
                'data' => null,
            ]);

        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());

        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}