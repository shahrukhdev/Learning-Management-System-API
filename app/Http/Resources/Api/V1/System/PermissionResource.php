<?php

namespace App\Http\Resources\Api\V1\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'guard_name' => $this->guard_name ?? '',
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
