<?php

use Laravel\Sanctum\Http\Middleware\AuthenticateSession;
use Laravel\Sanctum\Http\Middleware\EncryptCookies;
use Laravel\Sanctum\Http\Middleware\ValidateCsrfToken;

return [

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', '')),

    'guard' => ['web'],

    'expiration' => 1440,

    'token_prefix' => '',

    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],

];
