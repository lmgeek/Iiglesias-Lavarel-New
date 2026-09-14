<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date?->toISOString(),
            'end_date' => $this->end_date?->toISOString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_recurring' => $this->is_recurring,
            'recurring_frequency' => $this->recurring_frequency,
            'recurring_end_date' => $this->recurring_end_date?->toISOString(),
            'recurring_days' => $this->recurring_days,
            'color' => $this->color,
            'all_day' => $this->all_day,
            'location' => $this->location,
            'created_by' => $this->created_by,
            'creator' => $this->whenLoaded('creator', fn () => new UserResource($this->creator)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
