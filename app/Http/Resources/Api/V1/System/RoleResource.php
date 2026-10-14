<?php

namespace App\Http\Resources\Api\V1\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'guard_name' => $this->guard_name ?? '',
            'permissions' => PermissionResource::collection(
                $this->whenLoaded('permissions')
            ),
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}