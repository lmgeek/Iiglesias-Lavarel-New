<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->hasRole('Admin')) {
            return response()->json(['error' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        return $next($request);
    }
}
