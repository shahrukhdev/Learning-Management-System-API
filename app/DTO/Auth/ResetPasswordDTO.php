<?php

namespace App\DTO\Auth;

use Illuminate\Http\Request;

class ResetPasswordDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly string $password,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            email: $request->input('email'),
            token: $request->input('token'),
            password: $request->input('password'),
        );
    }
}