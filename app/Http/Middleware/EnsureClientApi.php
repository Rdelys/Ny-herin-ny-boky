<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque les comptes vendeur sur les routes API "acheteur" (profil,
 * commandes...). Renvoie une erreur JSON claire plutôt qu'une redirection
 * web, puisque cette API n'a pas de vue.
 */
class EnsureClientApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isClient()) {
            return response()->json([
                'message' => "Cette action est réservée aux comptes acheteurs.",
            ], 403);
        }

        return $next($request);
    }
}
