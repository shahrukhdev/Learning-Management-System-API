
<?php

namespace App\Http\Resources\Api\V1\Hifz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HifzStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource;
    }
}
