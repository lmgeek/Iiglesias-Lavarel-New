<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'mentor_id' => $this->mentor_id,
            'disciple_id' => $this->disciple_id,
            'f_meet' => $this->f_meet?->toISOString(),
            'suspended' => $this->suspended,
            'why_suspended' => $this->why_suspended,
            'theme_meetings_id' => $this->theme_meetings_id,
            'other_theme' => $this->other_theme,
            'culminate' => $this->culminate,
            'initiative' => $this->initiative,
            'reading' => $this->reading,
            'testimonials' => $this->testimonials,
            'pray_together' => $this->pray_together,
            'description' => $this->description,
            'uuid' => $this->uuid,
            'mentor' => $this->whenLoaded('mentor', fn () => new UserResource($this->mentor)),
            'disciple' => $this->whenLoaded('disciple', fn () => new UserResource($this->disciple)),
            'theme' => $this->whenLoaded('theme', fn () => new MeetingsThemeResource($this->theme)),
            'reports' => $this->whenLoaded('reports', fn () => ReportCelulaCollection::make($this->reports)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
