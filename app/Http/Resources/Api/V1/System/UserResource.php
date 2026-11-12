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
            'initials' => $user->initials ?? '',
            'email' => $this->email ?? '',
            'phone_no' => $this->phone_no ?? '',
            'image' => $this->image,
            'is_active' => (bool) $this->is_active,
            'last_login' => formatLastLogin($this->last_login),
            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),
            $this->mergeWhen($this->relationLoaded('student'), fn () => $this->studentClass()),
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }

    /**
     * The class of the user's active student record (null when not a student).
     */
    private function studentClass(): array
    {
        $class = $this->student?->academicClasses->first();

        return [
            'class_id' => $class?->id,
            'class' => $class ? [
                'id' => $class->id,
                'name' => $class->name,
            ] : null,
        ];
    }
}
