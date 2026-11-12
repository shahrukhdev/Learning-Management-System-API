<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HifzSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'recorded_by' => $this->recorded_by,
            'recorded_by_user' => $this->whenLoaded('recordedBy', fn () => [
                'id' => $this->recordedBy?->id,
                'name' => $this->recordedBy?->name,
            ]),
            'session_date' => $this->session_date?->format('Y-m-d'),
            'notes' => $this->notes,

            'old_sabaq' => HifzOldSabaqEntryResource::collection(
                $this->whenLoaded('oldSabaqEntries')
            ),

            'manzil' => HifzManzilEntryResource::collection(
                $this->whenLoaded('manzilEntries')
            ),

            'new_sabaq' => HifzNewSabaqEntryResource::collection(
                $this->whenLoaded('newSabaqEntries')
            ),
        ];
    }
}
