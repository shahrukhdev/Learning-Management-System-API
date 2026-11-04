<?php

namespace App\DTOs\Hifz;

use Illuminate\Http\Request;

readonly class UpdateStudentSurahProgressDTO
{
    public function __construct(
        public string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->status,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}