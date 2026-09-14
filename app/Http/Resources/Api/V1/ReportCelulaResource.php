<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportCelulaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'mentor_id' => $this->mentor_id,
            'f_meet' => $this->f_meet?->toISOString(),
            'celula' => $this->celula,
            'suspended' => $this->suspended,
            'why_suspended' => $this->why_suspended,
            'lider' => $this->lider,
            'message_title' => $this->message_title,
            'who_meet' => $this->who_meet,
            'format' => $this->format,
            'people_qty' => $this->people_qty,
            'new_people_qty' => $this->new_people_qty,
            'mentoring' => $this->mentoring,
            'observations' => $this->observations,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
