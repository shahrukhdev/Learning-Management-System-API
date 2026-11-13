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
            'initials' => $this->initials ?? '',
            'email' => $this->email ?? '',
            'phone_no' => $this->phone_no ?? '',
            'image' => $this->image,
            'is_active' => (bool) $this->is_active,
            'last_login' => formatLastLogin($this->last_login),

            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),

            $this->mergeWhen(
                $this->relationLoaded('student'),
                fn () => $this->studentClass()
            ),

            $this->mergeWhen(
                $this->relationLoaded('teacher'),
                fn () => $this->teacherClasses()
            ),

            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }

    /**
     * The class of the user's active student record.
     */
    private function studentClass(): array
    {
        $class = $this->student?->academicClasses->first();

        return [
            'year' => $this->student?->year,
            'class_id' => $class?->id,
            'class' => $class ? [
                'id' => $class->id,
                'name' => $class->name,
            ] : null,
        ];
    }

    /**
     * Academic classes assigned to the user's active teacher record.
     */
    private function teacherClasses(): array
    {
        $classes = $this->teacher?->academicClasses ?? collect();

        return [
            'class_ids' => $classes->pluck('id')->values(),
        ];
    }
}
