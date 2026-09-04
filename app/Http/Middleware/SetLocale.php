<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['fr', 'mg', 'en'];

    /**
     * Applique la langue sur TOUTES les routes (y compris /profil, /vendeur,
     * les formulaires POST...), pas seulement la page d'accueil.
     *
     * - Si l'URL commence par /fr, /mg ou /en, cette langue est mémorisée en session.
     * - Sinon, on relit la langue mémorisée en session.
     * - Sinon, on retombe sur la langue par défaut de l'app (config('app.locale')).
     *
     * C'est ce qui corrige le bug : avant, la locale n'était fixée que pour les
     * routes de la page d'accueil ; toute autre page (comme /profil) retombait
     * sur la locale par défaut de Laravel (souvent "en").
     */
    public function handle(Request $request, Closure $next): Response
    {
        $prefix = $request->segment(1);

        if (in_array($prefix, $this->supported, true)) {
            $locale = $prefix;
            session(['locale' => $locale]);
        } elseif (in_array(session('locale'), $this->supported, true)) {
            $locale = session('locale');
        } else {
            $locale = config('app.locale', 'fr');
        }

        App::setLocale($locale);

        return $next($request);
    }
}