<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One row of the student's own attendance history.
 */
class MyAttendanceRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->attendance_date?->format('Y-m-d'),
            'day' => $this->attendance_date?->format('l'),
            'status' => $this->status?->value,
            'class_name' => $this->academicClass?->name,
            'marked_at' => $this->updated_at?->format('H:i'),
        ];
    }
}
