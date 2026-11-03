
<?php

namespace App\DTOs\Hifz;

use Illuminate\Http\Request;

readonly class StoreHifzSessionDTO
{
    public function __construct(
        public int $student_id,
        public int $teacher_id,
        public string $session_date,
        public ?string $notes,
        public array $old_sabaq,
        public array $manzil,
        public array $new_sabaq,
    ) {}

    public static function fromRequest(
        Request $request,
        int $studentId,
        int $teacherId
    ): self {
        return new self(
            student_id: $studentId,
            teacher_id: $teacherId,
            session_date: $request->session_date,
            notes: $request->notes,
            old_sabaq: $request->input('old_sabaq', []),
            manzil: $request->input('manzil', []),
            new_sabaq: $request->input('new_sabaq', []),
        );
    }

    public function toArray(): array
    {
        return [
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'session_date' => $this->session_date,
            'notes' => $this->notes,
        ];
    }
}
