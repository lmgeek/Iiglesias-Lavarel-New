<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAPI / Swagger Documentation Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file provides the necessary settings for generating
    | OpenAPI (formerly Swagger) documentation for your API using the
    | zircote/swagger-php library.
    |
    */

    'default' => [
        'api' => [
            'title' => env('APP_NAME', 'Catedral Cristiana API'),
            'version' => env('APP_VERSION', '1.0.0'),
            'description' => 'API REST para la plataforma de discipulado y gestión ministerial de Catedral Cristiana',
            'termsOfService' => env('APP_TOS_URL', ''),
            'contact' => [
                'name' => env('APP_CONTACT_NAME', 'Catedral Cristiana'),
                'email' => env('APP_CONTACT_EMAIL', 'noreply@catedralcristiana.com'),
                'url' => env('APP_CONTACT_URL', ''),
            ],
            'license' => [
                'name' => env('APP_LICENSE_NAME', 'MIT'),
                'url' => env('APP_LICENSE_URL', 'https://opensource.org/licenses/MIT'),
            ],
            'servers' => [
                [
                    'url' => env('APP_URL', 'http://localhost:8000').'/api/v1',
                    'description' => 'Development Server',
                ],
            ],
        ],
        'routes' => [
            'api' => '/api/v1/*',
            'sanctum' => '/sanctum/*',
        ],
        'paths' => [
            'docs' => storage_path('api-docs'),
            'annotations' => base_path('app/Http/Controllers/Api'),
            'base' => base_path('app'),
        ],
        'securityDefinitions' => [
            'bearerAuth' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
            'sanctum' => [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'Authorization',
                'description' => 'Laravel Sanctum Personal Access Token',
            ],
        ],
        'security' => [
            ['bearerAuth' => []],
            ['sanctum' => []],
        ],
        'formats' => ['json'],
        'validatorUrl' => null,
        'formats' => ['json'],
    ],

];
