<?php

namespace App\DTOs\Notice;

use App\Http\Requests\Api\V1\Notice\UpdateNoticeRequest;

readonly class UpdateNoticeDTO
{
    public function __construct(
        public ?string $title,
        public ?string $message,
        public ?bool $isActive,
        public bool $titleProvided,
        public bool $messageProvided,
        public bool $isActiveProvided,
    ) {}

    public static function fromRequest(UpdateNoticeRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            title: $validated['title'] ?? null,
            message: $validated['message'] ?? null,
            isActive: $validated['is_active'] ?? null,
            titleProvided: array_key_exists('title', $validated),
            messageProvided: array_key_exists('message', $validated),
            isActiveProvided: array_key_exists('is_active', $validated),
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->titleProvided) {
            $data['title'] = $this->title;
        }

        if ($this->messageProvided) {
            $data['message'] = $this->message;
        }

        if ($this->isActiveProvided) {
            $data['is_active'] = $this->isActive;
        }

        return $data;
    }
}