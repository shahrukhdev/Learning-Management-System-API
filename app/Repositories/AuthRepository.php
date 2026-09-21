<?php

namespace App\Repositories;

use App\Contracts\AuthInterface;
use App\DTO\Auth\ResetPasswordDTO;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthRepository implements AuthInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createToken(User $user): string
    {
        return $user->createToken('auth-token')->plainTextToken;
    }

    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink([
            'email' => $email,
        ]);
    }

    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $status = Password::reset(
            [
                'email' => $dto->email,
                'password' => $dto->password,
                'password_confirmation' => $dto->password,
                'token' => $dto->token,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new ApiException(
                message: __($status),
                status: 422
            );
        }
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

}
