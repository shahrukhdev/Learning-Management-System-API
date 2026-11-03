
<?php

namespace App\DTOs\Academic;

use App\Http\Requests\Api\V1\Academic\UpdateStudentRequest;

readonly class UpdateStudentDTO
{
    public function __construct(
        public ?int $user_id,
        public ?string $admission_number,
        public ?string $date_of_birth,
        public ?string $gender,
        public ?string $phone,
        public ?string $address,
        public bool $userIdProvided,
        public bool $admissionNumberProvided,
        public bool $dateOfBirthProvided,
        public bool $genderProvided,
        public bool $phoneProvided,
        public bool $addressProvided,
    ) {}

    public static function fromRequest(UpdateStudentRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            user_id: $validated['user_id'] ?? null,
            admission_number: $validated['admission_number'] ?? null,
            date_of_birth: $validated['date_of_birth'] ?? null,
            gender: $validated['gender'] ?? null,
            phone: $validated['phone'] ?? null,
            address: $validated['address'] ?? null,
            userIdProvided: array_key_exists('user_id', $validated),
            admissionNumberProvided: array_key_exists('admission_number', $validated),
            dateOfBirthProvided: array_key_exists('date_of_birth', $validated),
            genderProvided: array_key_exists('gender', $validated),
            phoneProvided: array_key_exists('phone', $validated),
            addressProvided: array_key_exists('address', $validated),
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->userIdProvided) {
            $data['user_id'] = $this->user_id;
        }

        if ($this->admissionNumberProvided) {
            $data['admission_number'] = $this->admission_number;
        }

        if ($this->dateOfBirthProvided) {
            $data['date_of_birth'] = $this->date_of_birth;
        }

        if ($this->genderProvided) {
            $data['gender'] = $this->gender;
        }

        if ($this->phoneProvided) {
            $data['phone'] = $this->phone;
        }

        if ($this->addressProvided) {
            $data['address'] = $this->address;
        }

        return $data;
    }
}
