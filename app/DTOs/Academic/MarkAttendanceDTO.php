<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\MarkAttendanceRequest;

readonly class MarkAttendanceDTO
{
    public function __construct(
        public int $academicClassId,
        public string $attendanceDate,
        public array $attendances,
    ) {}

    public static function fromRequest(MarkAttendanceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            academicClassId: $validated['academic_class_id'],
            attendanceDate: $validated['attendance_date'],
            attendances: $validated['attendances'],
        );
    }

    public function toArray(): array
    {
        return [
            'academic_class_id' => $this->academicClassId,
            'attendance_date' => $this->attendanceDate,
            'attendances' => $this->attendances,
        ];
    }
}