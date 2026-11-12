<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\StoreStudentProgressRequest;

readonly class StoreStudentProgressDTO
{
    public function __construct(
        public int $student_id,
        public int $course_id,
        public int $recorded_by,
        public string $lesson,
        public string $grade,
        public ?string $notes,
        public string $progress_date,
    ) {}

    public static function fromRequest(
        StoreStudentProgressRequest $request,
        int $studentId,
        int $recordedBy
    ): self {
        $data = $request->validated();

        return new self(
            student_id: $studentId,
            course_id: (int) $data['course_id'],
            recorded_by: $recordedBy,
            lesson: trim($data['lesson']),
            grade: $data['grade'],
            notes: isset($data['notes']) && trim($data['notes']) !== ''
                ? trim($data['notes'])
                : null,
            progress_date: $data['progress_date'] ?? now()->toDateString(),
        );
    }

    public function toArray(): array
    {
        return [
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'recorded_by' => $this->recorded_by,
            'lesson' => $this->lesson,
            'grade' => $this->grade,
            'notes' => $this->notes,
            'progress_date' => $this->progress_date,
        ];
    }
}
