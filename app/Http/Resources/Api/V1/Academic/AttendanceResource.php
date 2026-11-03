<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attendance = $this->attendances->first();

        return [
            'student_id' => $this->id,
            'admission_number' => $this->admission_number,
            'name' => $this->user?->name ?? '',
            'image' => $this->user?->image,
            'attendance_id' => $attendance?->id,
            'status' => $attendance?->status?->value ?? 'not-marked',
        ];
    }
}