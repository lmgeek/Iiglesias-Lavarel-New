<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingsThemeCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(fn ($t) => new MeetingsThemeResource($t)),
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}
