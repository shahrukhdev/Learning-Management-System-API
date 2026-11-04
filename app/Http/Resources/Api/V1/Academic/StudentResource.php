<?php

namespace App\Http\Resources\Api\V1\Academic;

use App\Http\Resources\Api\V1\System\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'admission_number' => $this->admission_number,
            'date_of_birth' => $this->date_of_birth?->format('d-m-Y'),
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'user' => UserResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
