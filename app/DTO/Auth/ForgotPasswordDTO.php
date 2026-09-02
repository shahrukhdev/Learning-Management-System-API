<?php

namespace App\DTO\Auth;

use Illuminate\Http\Request;

class ForgotPasswordDTO
{
    public function __construct(public readonly string $email) 
    {
        //
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            email: $request->input('email'),
        );
    }
}