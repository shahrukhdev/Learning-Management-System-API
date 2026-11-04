<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\UpdateCourseRequest;

readonly class UpdateCourseDTO
{
    public function __construct(
        public string $name,
        public ?string $code,
        public ?string $description,
        public ?int $duration,
        public ?bool $is_active,
        public ?array $academic_class_ids,
        public bool $codeProvided,
        public bool $descriptionProvided,
        public bool $durationProvided,
    ) {}

    public static function fromRequest(UpdateCourseRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: $data['code'] ?? null,
            description: $data['description'] ?? null,
            duration: isset($data['duration']) ? (int) $data['duration'] : null,
            is_active: array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : null,
            academic_class_ids: array_key_exists('academic_class_ids', $data)
                ? $data['academic_class_ids']
                : null,
            codeProvided: array_key_exists('code', $data),
            descriptionProvided: array_key_exists('description', $data),
            durationProvided: array_key_exists('duration', $data),
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

        if ($this->durationProvided) {
            $data['duration'] = $this->duration;
        }

        if ($this->is_active !== null) {
            $data['is_active'] = $this->is_active;
        }

        return $data;
    }
}