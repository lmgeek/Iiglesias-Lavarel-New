<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingsThemeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'classname' => $this->classname,
            'image' => $this->image,
            'youtube' => $this->youtube,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
