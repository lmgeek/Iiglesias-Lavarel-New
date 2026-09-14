<?php

// instalar.php — ejecuta comandos de Artisan sin terminal. ¡BORRAR después de usarlo!
// Uso: https://www.tudominio.com/instalar.php?token=TU_INSTALL_TOKEN

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();

$secret = env('INSTALL_TOKEN', '');

if ($secret === '' || ! isset($_GET['token']) || ! hash_equals($secret, $_GET['token'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

set_time_limit(300);

function run(string $cmd): void
{
    echo "<pre style='background:#111;color:#0f0;padding:10px'>$ php artisan {$cmd}</pre>";
    Artisan::call($cmd);
    echo '<pre>'.htmlspecialchars(Artisan::output()).'</pre>';
    flush();
}

run('migrate --force');
run('db:seed --force');
run('storage:link');

echo '<p><strong>Instalación finalizada. ¡BORRÁ instalar.php y quita INSTALL_TOKEN del .env!</strong></p>';