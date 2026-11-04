<?php

namespace App\DTOs\Hifz;

use Illuminate\Http\Request;

readonly class StoreStudentSurahProgressDTO
{
    public function __construct(
        public int $student_id,
        public int $surah_id,
        public string $status,
    ) {}

    public static function fromRequest(Request $request, int $studentId): self
    {
        return new self(
            student_id: $studentId,
            surah_id: (int) $request->surah_id,
            status: $request->status,
        );
    }

    public function toArray(): array
    {
        return [
            'student_id' => $this->student_id,
            'surah_id' => $this->surah_id,
            'status' => $this->status,
        ];
    }
}