<?php

namespace App\Contracts;

use App\Models\User;

interface AuthInterface
{
    public function findByEmail(string $email): ?User;

    public function login(User $user): void;

    public function logout(): void;

    public function user(): ?User;

    public function sendResetLink(string $email): string;

    public function resetPassword(string $email, string $token, string $password): string;
}
