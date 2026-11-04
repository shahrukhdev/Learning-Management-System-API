<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\StoreCourseRequest;

readonly class StoreCourseDTO
{
    public function __construct(
        public string $name,
        public ?string $code,
        public ?string $description,
        public ?int $duration,
        public bool $is_active,
        public array $academic_class_ids,
    ) {}

    public static function fromRequest(StoreCourseRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
            duration: isset($data['duration']) ? (int) $data['duration'] : null,
            is_active: (bool) ($data['is_active'] ?? true),
            academic_class_ids: $data['academic_class_ids'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'duration' => $this->duration,
            'is_active' => $this->is_active,
        ];
    }
}