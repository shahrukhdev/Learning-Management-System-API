<?php

namespace App\Contracts;

use App\DTO\Auth\ResetPasswordDTO;
use App\Models\User;

interface AuthInterface
{
    public function findByEmail(string $email): ?User;

    public function createToken(User $user): string;

    public function sendPasswordResetLink(string $email): string;

    public function resetPassword(ResetPasswordDTO $dto): void;

    public function logout(User $user): void;
}
