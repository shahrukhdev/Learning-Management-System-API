<?php

namespace App\Services\Auth;

use App\DTO\Auth\ForgotPasswordDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\ResetPasswordDTO;
use App\Models\User;
use App\Contracts\Auth\AuthInterface;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function __construct(protected AuthInterface $repository) 
    {
        //
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->repository->findByEmail($dto->email);

        if (!$user || !$user->is_active || !Hash::check($dto->password, $user->password)) {
            throw new ApiException(
                message: 'These credentials do not match our records.',
                status: 401
            );
        }

        $token = $this->repository->createToken($user);

        $this->repository->updateLastLogin($user);

        return [
            'user' => $user->fresh(),
            'token' => $token,
        ];
    }

    public function forgotPassword(ForgotPasswordDTO $dto): void
    {
        $status = $this->repository->sendPasswordResetLink($dto->email);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new ApiException(
                __($status),
                422
            );
        }
    }

    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $this->repository->resetPassword($dto);
    }

    public function logout(User $user): void
    {
        $this->repository->logout($user);
    }
}
