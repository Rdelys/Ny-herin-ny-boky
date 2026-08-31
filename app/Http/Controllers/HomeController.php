<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    /**
     * Locales handled by the site. "fr" is the default and lives at "/".
     */
    protected array $locales = ['fr', 'mg', 'en'];

    public function index(Request $request, string $locale = 'fr')
    {
        if (! in_array($locale, $this->locales, true)) {
            abort(404);
        }

        App::setLocale($locale);

        return view('home', [
            // 'books'   => \App\Models\Book::latest()->take(4)->get(),
            // 'sellers' => \App\Models\Seller::withCount('books')->take(3)->get(),
        ]);
    }
}