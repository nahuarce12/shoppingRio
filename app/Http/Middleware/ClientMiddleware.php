<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
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

        if (!Auth::user()->isClient()) {
            abort(403, 'Acceso denegado. Esta sección es solo para clientes.');
        }

        // Optional: Check email verification for clients
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Debe verificar su correo electrónico para continuar.');
        }

        return $next($request);
    }
}
