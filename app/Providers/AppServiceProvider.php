<?php

namespace App\Providers;

use App\Models\ChurchConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar cache en memoria: no se persiste nada en disco
        if (config('cache.default') !== 'array') {
            config(['cache.default' => 'array']);
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Paginator::defaultView('vendor.pagination.app');

        View::composer('*', function ($view) {
            $view->with('config', ChurchConfig::getConfig());
        });
    }
}
