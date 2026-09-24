<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'newsletter_email' => ['required', 'email', 'max:190'],
        ]);

        // Déjà inscrit : message dédié plutôt que l'erreur "unique" brute.
        $dejaInscrit = NewsletterSubscriber::where('email', $data['newsletter_email'])->exists();

        if ($dejaInscrit) {
            return back()->with('newsletter_info', __('home.newsletter_already'))->withInput();
        }

        NewsletterSubscriber::create(['email' => $data['newsletter_email']]);

        return back()->with('newsletter_success', __('home.newsletter_success'));
    }
}