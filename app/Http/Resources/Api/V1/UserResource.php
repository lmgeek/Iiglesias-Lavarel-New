<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'fullname' => $this->fullname,
            'born_date' => $this->born_date,
            'sex' => $this->sex,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone' => $this->phone,
            'church' => $this->church,
            'mentor' => $this->mentor,
            'ministerial_range' => $this->ministerial_range,
            'celula' => $this->celula,
            'doc_number' => $this->doc_number,
            'lider_celula' => $this->lider_celula,
            'google_id' => $this->google_id,
            'is_active' => true,
            'must_change_password' => false,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->toArray()),
            'permissions' => $this->whenLoaded('permissions', fn () => $this->getAllPermissions()->pluck('name')->toArray()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
