<?php

namespace App\Console\Commands;

use App\Services\WebPushService;
use Illuminate\Console\Command;

class WebPushEnviar extends Command
{
    protected $signature = 'webpush:enviar';

    protected $description = 'Envía notificaciones push pendientes';

    public function handle(WebPushService $webPushService)
    {
        $this->info('Enviando notificaciones push...');

        $sent = $webPushService->sendToAll([
            'title' => 'Catedral Cristiana',
            'body' => 'Nueva notificación disponible',
            'icon' => '/icon-192.png',
            'data' => ['url' => '/dashboard'],
        ]);

        $this->info("Notificaciones enviadas: {$sent}");

        return 0;
    }
}
