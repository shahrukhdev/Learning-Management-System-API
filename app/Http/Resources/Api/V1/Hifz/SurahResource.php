<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'name' => $this->name,
            'arabic' => $this->arabic,
            'ayahs' => $this->ayahs,
        ];
    }
}