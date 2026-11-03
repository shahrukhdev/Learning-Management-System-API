<?php

namespace App\DTOs\Notice;

use App\Http\Requests\Api\V1\Notice\StoreNoticeRequest;

readonly class StoreNoticeDTO
{
    public function __construct(
        public string $title,
        public string $message,
        public bool $isActive,
    ) {}

    public static function fromRequest(StoreNoticeRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            title: $validated['title'],
            message: $validated['message'],
            isActive: $validated['is_active'],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'is_active' => $this->isActive,
        ];
    }
}