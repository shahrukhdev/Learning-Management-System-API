<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\StoreTeacherRequest;

readonly class CreateTeacherDTO
{
    public function __construct(
        public ?int $userId,
    ) {}

    public static function fromRequest(StoreTeacherRequest $request): self
    {
        return new self(
            userId: $request->validated('user_id'),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
        ];
    }
}