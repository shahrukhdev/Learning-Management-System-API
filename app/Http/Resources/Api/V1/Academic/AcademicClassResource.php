<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'code' => $this->code ?? '',
            'description' => $this->description ?? '',
            'capacity' => $this->capacity,
            'is_active' => (bool) $this->is_active,
            'teacher_ids' => $this->whenLoaded(
                'teachers',
                fn () => $this->teachers->pluck('id')->values()
            ),
            'teachers' => $this->whenLoaded(
                'teachers',
                fn () => $this->teachers->map(fn ($teacher) => [
                    'id' => $teacher->id,
                    'user_id' => $teacher->user_id,
                    'name' => $teacher->user?->name ?? '',
                ])->values()
            ),
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}