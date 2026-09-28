<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Accès simple pour l'instant : un flag en session posé par
     * AdminAuthController::login(), pas de table "admins" en base.
     * À faire évoluer plus tard vers un vrai système (table admins,
     * rôles, etc.) une fois le reste confirmé.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('is_admin')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}