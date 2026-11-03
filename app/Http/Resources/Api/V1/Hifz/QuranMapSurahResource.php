<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuranMapSurahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'name' => $this->name,
            'arabic' => $this->arabic,
            'ayahs' => $this->ayahs,

            'status' => $this->studentProgress
                ->first()?->status ?? 'not-started',

            'juz_ranges' => $this->juzSurahRanges->map(fn ($range) => [
                'juz_number' => $range->juz->number,
                'start_ayah' => $range->start_ayah,
                'end_ayah' => $range->end_ayah,
            ])->values(),
        ];
    }
}