<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JuzSurahRangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surah' => [
                'id' => $this->surah->id,
                'number' => $this->surah->number,
                'name' => $this->surah->name,
                'arabic' => $this->surah->arabic,
                'ayahs' => $this->surah->ayahs,
            ],
            'start_ayah' => $this->start_ayah,
            'end_ayah' => $this->end_ayah,
        ];
    }
}