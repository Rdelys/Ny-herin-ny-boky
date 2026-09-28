<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * Sitemap et robots.txt générés dynamiquement : les fiches vendeurs
 * apparaissent dès leur inscription, sans fichier statique à maintenir.
 * Les URLs privées (profil, back-office) sont volontairement exclues.
 */
class SitemapController extends Controller
{
    /** Langues servies sur la home, pour les balises hreflang. */
    private const LOCALES = ['fr', 'mg', 'en'];

    public function sitemap(): Response
    {
        $urls = [];

        // Accueil : une entrée par langue, chacune pointant vers les autres.
        $alternates = ['x-default' => url('/')];
        foreach (self::LOCALES as $locale) {
            $alternates[$locale] = $locale === 'fr' ? url('/') : url('/' . $locale);
        }

        foreach ($alternates as $hreflang => $loc) {
            if ($hreflang === 'x-default') {
                continue;
            }

            $urls[] = [
                'loc' => $loc,
                'changefreq' => 'daily',
                'priority' => $hreflang === 'fr' ? '1.0' : '0.9',
                'alternates' => $alternates,
            ];
        }

        $dernierLivre = Book::max('updated_at');

        $urls[] = [
            'loc' => route('books.index'),
            'lastmod' => $dernierLivre ? Carbon::parse($dernierLivre)->toAtomString() : null,
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];
        $urls[] = ['loc' => route('sellers.index'), 'changefreq' => 'daily', 'priority' => '0.8'];

        foreach (['pages.about', 'pages.privacy', 'pages.terms'] as $route) {
            $urls[] = ['loc' => route($route), 'changefreq' => 'monthly', 'priority' => '0.4'];
        }

        $sellers = User::query()
            ->where('role', 'vendeur')
            ->select('id', 'updated_at')
            ->get();

        foreach ($sellers as $seller) {
            $urls[] = [
                'loc' => route('sellers.show', $seller),
                'lastmod' => $seller->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /profil',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
