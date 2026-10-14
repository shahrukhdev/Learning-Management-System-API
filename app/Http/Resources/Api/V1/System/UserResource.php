<?php

namespace App\Http\Resources\Api\V1\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'is_active' => (bool) $this->is_active,
            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}