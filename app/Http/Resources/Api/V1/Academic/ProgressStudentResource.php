<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A student as shown in the Student Progress module (list rows and profile card).
 */
class ProgressStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $class = $this->academicClasses->first();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $user?->name ?? '',
            'initials' => $user?->initials ?? '',
            'email' => $user?->email ?? '',
            'image' => $user?->image,
            'admission_number' => $this->admission_number ?? '',
            'class_id' => $class?->id,
            'class_name' => $class?->name ?? '',
            'total_records' => $this->when(
                isset($this->progress_records_count),
                fn () => (int) $this->progress_records_count
            ),
            'last_progress_date' => $this->when(
                array_key_exists('progress_records_max_progress_date', $this->getAttributes()),
                fn () => $this->progress_records_max_progress_date
                    ? date('Y-m-d', strtotime($this->progress_records_max_progress_date))
                    : null
            ),
        ];
    }
}
