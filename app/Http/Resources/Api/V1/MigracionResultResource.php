<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MigracionResultResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'success' => $this->success,
            'source_db' => $this->source_db,
            'counts' => $this->counts,
            'warnings' => $this->warnings ?? [],
        ];
    }
}
