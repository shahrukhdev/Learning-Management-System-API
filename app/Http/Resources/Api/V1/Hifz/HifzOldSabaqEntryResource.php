<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HifzOldSabaqEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_surah' => [
                'id' => $this->fromSurah->id,
                'number' => $this->fromSurah->number,
                'name' => $this->fromSurah->name,
                'arabic' => $this->fromSurah->arabic,
            ],
            'from_ayah' => $this->from_ayah,
            'to_surah' => [
                'id' => $this->toSurah->id,
                'number' => $this->toSurah->number,
                'name' => $this->toSurah->name,
                'arabic' => $this->toSurah->arabic,
            ],
            'to_ayah' => $this->to_ayah,
            'page' => $this->page ?? 0,
            'status' => $this->status,
            'strength' => $this->strength,
            'mistakes' => $this->mistakes,
        ];
    }
}
