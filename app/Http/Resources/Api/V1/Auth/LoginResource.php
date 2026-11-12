<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource['user'];

        return [
            'status' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => $user->initials ?? '',
                    'email' => $user->email,
                    'phone_no' => $user->phone_no ?? '',
                    'is_active' => $user->is_active,
                    'image' => $user->image,
                    'role' => $user->getRoleNames()->first() ?? '',
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                ],
                'token' => $this->resource['token'] ?? '',
            ],
        ];
    }
}
