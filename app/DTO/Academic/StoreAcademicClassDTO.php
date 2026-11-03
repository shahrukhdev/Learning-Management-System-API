<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\StoreAcademicClassRequest;

readonly class StoreAcademicClassDTO
{
    public function __construct(
        public string $name,
        public ?string $code,
        public ?string $description,
        public ?int $capacity,
        public bool $is_active,
        public array $course_ids,
    ) {}

    public static function fromRequest(StoreAcademicClassRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null,
            is_active: (bool) ($data['is_active'] ?? true),
            course_ids: $data['course_ids'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
        ];
    }
}