<?php

namespace App\Http\Resources\Api\V1\Academic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attendances = $this->attendances->keyBy(
            fn ($attendance) => strtolower(
                $attendance->attendance_date->format('D')
            )
        );

        $weekDays = ['mon', 'tue', 'wed', 'thu', 'fri'];

        $attendanceData = [];

        foreach ($weekDays as $day) {
            $attendanceData[$day] = $attendances->get($day)?->status?->value
                ?? 'not-marked';
        }

        $totalMarkedDays = $attendances->count();

        $attendedDays = $attendances->filter(
            fn ($attendance) => in_array(
                $attendance->status->value,
                ['present', 'late']
            )
        )->count();

        $attendanceRate = $totalMarkedDays > 0
            ? round(($attendedDays / $totalMarkedDays) * 100, 2)
            : 0;

        return [
            'student_id' => $this->id,
            'student_name' => $this->user?->name ?? '',
            'student_image' => $this->user?->image,
            'attendance' => $attendanceData,
            'rate' => $attendanceRate,
        ];
    }
}