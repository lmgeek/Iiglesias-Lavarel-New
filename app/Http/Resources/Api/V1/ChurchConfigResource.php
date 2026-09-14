<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ChurchConfigResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'church_name' => $this->church_name,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
            'login_bg' => $this->login_bg,
            'phone' => $this->phone,
            'email' => $this->email,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
        ];
    }
}
