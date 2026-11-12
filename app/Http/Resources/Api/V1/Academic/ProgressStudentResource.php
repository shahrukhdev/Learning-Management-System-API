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
            $this->mergeWhen(
                isset($this->memorised_surahs_count),
                fn () => $this->hifzStats()
            ),
            $this->mergeWhen(
                isset($this->attendance_total_count),
                fn () => $this->attendanceStats()
            ),
        ];
    }

    /**
     * Hifz progress card, e.g. 25 / 114 surahs memorised = 21.9%.
     */
    private function hifzStats(): array
    {
        $memorised = (int) $this->memorised_surahs_count;
        $total = (int) ($this->total_surahs_count ?? 0);

        return [
            'memorised_surahs' => $memorised,
            'total_surahs' => $total,
            'hifz_percentage' => $total > 0 ? round(($memorised / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Attendance / Late / Absent stats. The attendance rate counts
     * present + late as attended (same as the dashboard).
     */
    private function attendanceStats(): array
    {
        $total = (int) $this->attendance_total_count;
        $present = (int) $this->attendance_present_count;
        $late = (int) $this->attendance_late_count;

        return [
            'attendance' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
            'present' => $present,
            'late' => $late,
            'absent' => (int) $this->attendance_absent_count,
            'total_attendance_days' => $total,
        ];
    }
}
