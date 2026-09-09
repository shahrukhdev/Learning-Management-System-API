<?php

namespace App\Services\Auth;

use App\DTO\Auth\ForgotPasswordDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\ResetPasswordDTO;
use App\Models\User;
use App\Contracts\AuthInterface;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function __construct(protected AuthInterface $authRepository) 
    {
        //
    }

    public function login(LoginDTO $loginDTO): User
    {
        $user = $this->authRepository->findByEmail($loginDTO->email);

        if (!$user || !Hash::check($loginDTO->password, $user->password)) {
            throw new ApiException(
                message: 'The provided credentials are incorrect.',
                status: 401
            );
        }

        $this->authRepository->login($user);

        return $user;
    }

    public function logout(): void
    {
        $this->authRepository->logout();
    }

    public function user(): ?User
    {
        return $this->authRepository->user();
    }

    public function forgotPassword(ForgotPasswordDTO $dto): void
    {
        $user = $this->authRepository->findByEmail($dto->email);

        if (!$user) {
            return;
        }

        $this->authRepository->sendResetLink($dto->email);
    }

    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $status = $this->authRepository->resetPassword($dto->email, $dto->token, $dto->password);

        if ($status !== Password::PASSWORD_RESET) {
            throw new ApiException(
                message: __($status),
                status: 422
            );
        }
    }
}
