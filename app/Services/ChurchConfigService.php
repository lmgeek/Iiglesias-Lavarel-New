<?php

namespace App\Services;

use App\Models\ChurchConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChurchConfigService
{
    public function getConfig(): array
    {
        $config = ChurchConfig::firstOrCreate([], [
            'church_name' => 'Catedral Cristiana',
            'logo' => null,
            'favicon' => null,
            'login_bg' => null,
        ]);

        return [
            'church_name' => $config->church_name,
            'logo' => $config->logo,
            'favicon' => $config->favicon,
            'login_bg' => $config->login_bg,
            'phone' => $config->phone,
            'email' => $config->email,
            'instagram' => $config->instagram,
            'facebook' => $config->facebook,
            'tiktok' => $config->tiktok,
            'youtube' => $config->youtube,
        ];
    }

    public function updateConfig(array $data): array
    {
        $config = ChurchConfig::firstOrCreate([]);

        $config->update([
            'church_name' => $data['church_name'] ?? $config->church_name,
            'logo' => $data['logo'] ?? $config->logo,
            'favicon' => $data['favicon'] ?? $config->favicon,
            'login_bg' => $data['login_bg'] ?? $config->login_bg,
            'phone' => $data['phone'] ?? $config->phone,
            'email' => $data['email'] ?? $config->email,
            'instagram' => $data['instagram'] ?? $config->instagram,
            'facebook' => $data['facebook'] ?? $config->facebook,
            'tiktok' => $data['tiktok'] ?? $config->tiktok,
            'youtube' => $data['youtube'] ?? $config->youtube,
        ]);

        return $this->getConfig();
    }

    public function uploadLogo(string $base64Image): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1];
            $filename = 'logo_'.time().'_'.Str::random(8).'.'.$extension;
        } else {
            $filename = 'logo_'.time().'_'.Str::random(8).'.png';
        }

        $path = 'church/logo/'.$filename;

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        }

        Storage::disk('public')->put($path, base64_decode($base64Image));

        return Storage::url($path);
    }

    public function generateFavicon(string $logoUrl): string
    {
        // En una implementación real, aquí se generaría el favicon
        // Por ahora devolvemos el logo como favicon
        return $logoUrl;
    }

    public function deleteLogo(): void
    {
        $config = ChurchConfig::first();
        if ($config && $config->logo) {
            $path = str_replace(Storage::url(''), '', $config->logo);
            Storage::disk('public')->delete($path);
        }
    }
}
