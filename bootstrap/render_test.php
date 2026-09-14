<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ViewErrorBag;

view()->share('errors', new ViewErrorBag());

$user = \App\Models\User::first();
Auth::login($user);

$html = view('layouts.app', [
    'config' => \App\Models\ChurchConfig::getConfig(),
])->render();

if (mb_strpos($html, 'function toggleTheme') !== false) {
    echo "toggleTheme: definida SI\n";
} else {
    echo "toggleTheme: MISSING\n";
}

preg_match('/fetch\(\s*\'([^\']+)\'/', $html, $m);
echo 'fetch url: '.($m[1] ?? 'MISSING')."\n";

preg_match('/<body[^>]*data-theme="([^"]+)"/', $html, $m2);
echo 'body data-theme: '.($m2[1] ?? 'MISSING')."\n";

preg_match('/<link rel="stylesheet" href="([^"]+app\.css[^"]*)"/', $html, $m3);
echo 'css link: '.($m3[1] ?? 'MISSING')."\n";

file_put_contents(__DIR__.'/rendered.html', $html);
echo "HTML guardado en bootstrap/rendered.html\n";