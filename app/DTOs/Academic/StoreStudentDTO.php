<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\StoreStudentRequest;

readonly class StoreStudentDTO
{
    public function __construct(
        public ?int $user_id,
        public string $admission_number,
        public ?string $date_of_birth,
        public ?string $gender,
        public ?string $phone,
        public ?string $address,
    ) {}

    public static function fromRequest(StoreStudentRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            user_id: $validated['user_id'] ?? null,
            admission_number: $validated['admission_number'],
            date_of_birth: $validated['date_of_birth'] ?? null,
            gender: $validated['gender'] ?? null,
            phone: $validated['phone'] ?? null,
            address: $validated['address'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'admission_number' => $this->admission_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
        ];
    }
}
