<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\UpdateTeacherRequest;

readonly class UpdateTeacherDTO
{
    public function __construct(
        public bool $userIdProvided,
        public ?int $userId,
    ) {}

    public static function fromRequest(UpdateTeacherRequest $request): self
    {
        return new self(
            userIdProvided: array_key_exists('user_id', $request->validated()),
            userId: $request->validated('user_id'),
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->userIdProvided) {
            $data['user_id'] = $this->userId;
        }

        return $data;
    }
}