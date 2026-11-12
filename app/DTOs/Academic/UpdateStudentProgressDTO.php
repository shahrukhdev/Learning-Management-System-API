<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\UpdateStudentProgressRequest;

/**
 * Partial update: only the fields sent in the request are changed.
 */
readonly class UpdateStudentProgressDTO
{
    public function __construct(
        public ?int $course_id,
        public ?string $lesson,
        public ?string $grade,
        public ?string $notes,
        public ?string $progress_date,
        public bool $notesProvided,
    ) {}

    public static function fromRequest(UpdateStudentProgressRequest $request): self
    {
        $data = $request->validated();

        return new self(
            course_id: isset($data['course_id']) ? (int) $data['course_id'] : null,
            lesson: isset($data['lesson']) ? trim($data['lesson']) : null,
            grade: $data['grade'] ?? null,
            notes: isset($data['notes']) && trim($data['notes']) !== ''
                ? trim($data['notes'])
                : null,
            progress_date: $data['progress_date'] ?? null,
            notesProvided: array_key_exists('notes', $data),
        );
    }

    public function toArray(): array
    {
        $data = array_filter([
            'course_id' => $this->course_id,
            'lesson' => $this->lesson,
            'grade' => $this->grade,
            'progress_date' => $this->progress_date,
        ], fn ($value) => $value !== null);

        if ($this->notesProvided) {
            $data['notes'] = $this->notes;
        }

        return $data;
    }
}
