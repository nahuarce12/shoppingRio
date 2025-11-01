<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        if (!Auth::user()->isStoreOwner()) {
            abort(403, 'Acceso denegado. Esta sección es solo para dueños de locales.');
        }

        if (!Auth::user()->isApproved()) {
            abort(403, 'Su cuenta de dueño de local aún no ha sido aprobada por un administrador.');
        }

        return $next($request);
    }
}
