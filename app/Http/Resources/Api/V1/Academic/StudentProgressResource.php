<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A single logged progress record.
 */
class StudentProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'subject' => $this->course?->name ?? '',
            'lesson' => $this->lesson ?? '',
            'grade' => $this->grade?->value ?? '',
            'notes' => $this->notes ?? '',
            'date' => $this->progress_date?->format('Y-m-d'),
            'recorded_by' => $this->recordedBy ? [
                'id' => $this->recordedBy->id,
                'name' => $this->recordedBy->name,
            ] : null,
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
