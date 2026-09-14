<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImgbbService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.imgbb.key') ?? '';
    }

    /**
     * Sube una imagen a imgBB.
     *
     * @return array{url: string|null, delete_url: string|null}|null null si falla
     */
    public function upload(UploadedFile $file): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('imgBB: API key no configurada (IMGBB_API_KEY)');

            return null;
        }

        try {
            if ($file->getSize() > 32 * 1024 * 1024) {
                Log::error('imgBB: imagen excede 32MB');

                return null;
            }

            $response = Http::timeout(30)
                ->asMultipart()
                ->post('https://api.imgbb.com/1/upload', [
                    'key' => $this->apiKey,
                    'image' => base64_encode($file->get()),
                ]);

            if (! $response->successful()) {
                Log::error('imgBB: upload falló', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json('data') ?? [];

            return [
                'url' => $data['url'] ?? null,
                'delete_url' => $data['delete_url'] ?? null,
                'display_url' => $data['display_url'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('imgBB: excepción en upload', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Sube una imagen a imgBB a partir de un path local almacenado (storage público).
     */
    public function uploadFromPath(string $localPath): ?array
    {
        if (! file_exists($localPath)) {
            Log::error('imgBB: archivo local no existe', ['path' => $localPath]);

            return null;
        }

        $file = new UploadedFile($localPath, basename($localPath));

        return $this->upload($file);
    }

    public function deleteByUrl(string $deleteUrl): bool
    {
        if (empty($deleteUrl) || empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(20)->get($deleteUrl);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('imgBB: excepción en delete', ['message' => $e->getMessage()]);

            return false;
        }
    }

    public function hasKey(): bool
    {
        return ! empty($this->apiKey);
    }
}
