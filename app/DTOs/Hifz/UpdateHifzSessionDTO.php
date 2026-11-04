<?php

namespace App\DTOs\Hifz;

use Illuminate\Http\Request;

readonly class UpdateHifzSessionDTO
{
    public function __construct(
        public string $session_date,
        public ?string $notes,
        public array $old_sabaq,
        public array $manzil,
        public array $new_sabaq,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
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
            'session_date' => $this->session_date,
            'notes' => $this->notes,
        ];
    }
}
