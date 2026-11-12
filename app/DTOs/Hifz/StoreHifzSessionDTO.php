<?php

namespace App\DTOs\Hifz;

use Illuminate\Http\Request;

readonly class StoreHifzSessionDTO
{
    public function __construct(
        public int $student_id,
        public int $recorded_by,
        public string $session_date,
        public ?string $notes,
        public array $old_sabaq,
        public array $manzil,
        public array $new_sabaq,
    ) {}

    public static function fromRequest(
        Request $request,
        int $studentId,
        int $recordedBy
    ): self {
        return new self(
            student_id: $studentId,
            recorded_by: $recordedBy,
            session_date: $request->session_date,
            notes: $request->notes,
            old_sabaq: array_map(
                fn ($entry) => [
                    ...$entry,
                    'page' => $entry['page'] ?? 0,
                ],
                $request->input('old_sabaq', [])
            ),
            manzil: $request->input('manzil', []),
            new_sabaq: $request->input('new_sabaq', []),
        );
    }

    public function toArray(): array
    {
        return [
            'student_id' => $this->student_id,
            'recorded_by' => $this->recorded_by,
            'session_date' => $this->session_date,
            'notes' => $this->notes,
        ];
    }
}
