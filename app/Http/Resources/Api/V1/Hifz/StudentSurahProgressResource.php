<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSurahProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'surah' => [
                'id' => $this->surah->id,
                'number' => $this->surah->number,
                'name' => $this->surah->name,
                'arabic' => $this->surah->arabic,
                'ayahs' => $this->surah->ayahs,
            ],
        ];
    }
}