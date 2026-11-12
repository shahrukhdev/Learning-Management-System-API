<?php

namespace App\Http\Resources\Api\V1\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'initials' => $user->initials ?? '',
            'email' => $user->email,
            'phone_no' => $user->phone_no ?? '',
            'is_active' => (bool) $user->is_active,
            'image' => $user->image,
            'role' => $user->getRoleNames()->first() ?? '',
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ];
    }
}