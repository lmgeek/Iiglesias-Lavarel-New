<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Keep it simple, stupid.');
})->purpose('Display an inspiring quote')->hourly();
