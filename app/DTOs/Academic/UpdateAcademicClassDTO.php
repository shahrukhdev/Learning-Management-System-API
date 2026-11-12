<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\UpdateAcademicClassRequest;

readonly class UpdateAcademicClassDTO
{
    public function __construct(
        public string $name,
        public ?string $code,
        public ?string $description,
        public ?int $capacity,
        public ?bool $is_active,
        public ?array $course_ids,
        public ?array $teacher_ids,
        public bool $codeProvided,
        public bool $descriptionProvided,
        public bool $capacityProvided,
    ) {}

    public static function fromRequest(UpdateAcademicClassRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null,
            is_active: array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : null,
            course_ids: array_key_exists('course_ids', $data)
                ? $data['course_ids']
                : null,
            teacher_ids: array_key_exists('teacher_ids', $data)
                ? $data['teacher_ids']
                : null,
            codeProvided: array_key_exists('code', $data),
            descriptionProvided: array_key_exists('description', $data),
            capacityProvided: array_key_exists('capacity', $data),
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->codeProvided) {
            $data['code'] = $this->code;
        }

        if ($this->descriptionProvided) {
            $data['description'] = $this->description;
        }

        if ($this->capacityProvided) {
            $data['capacity'] = $this->capacity;
        }

        if ($this->is_active !== null) {
            $data['is_active'] = $this->is_active;
        }

        return $data;
    }
}