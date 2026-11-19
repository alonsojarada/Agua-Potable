<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActivo()) {
            abort(403, 'Tu cuenta no está activa.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Acceso restringido únicamente al administrador del sistema.');
        }

        return $next($request);
    }
}