<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber; // <-- ajouter cet import
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse; // <-- ajouter
use App\Mail\NewsletterMail;      // <-- ajouter
use Illuminate\Support\Facades\Mail; // <-- ajouter


class AdminSettingsController extends Controller
{
     public function edit(): View
    {
        return view('admin.parametres', [
            'tiers'           => Setting::commissionTiers(),
            'paymentAccounts' => Setting::paymentAccounts(),

            // ---- Newsletter (nom de paginator dédié pour ne pas
            // interférer si une autre pagination arrive un jour sur
            // cette page) ----
            'newsletterSubscribers' => NewsletterSubscriber::latest()
                ->paginate(10, ['*'], 'newsletter_page'),
            'newsletterTotal' => NewsletterSubscriber::count(),
        ]);
    }

    /**
     * Barème de commission par palier, éditable depuis l'admin.
     * Les clés écrites ici (commission_tier1_max, commission_tier1_rate, …)
     * sont EXACTEMENT celles relues par Setting::commissionTiers().
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tier1_max'  => ['required', 'integer', 'min:0'],
            'tier2_max'  => ['required', 'integer', 'gt:tier1_max'],
            'tier1_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'tier2_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'tier3_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'tier2_max.gt' => 'Le 2ᵉ seuil doit être supérieur au 1ᵉʳ seuil.',
        ]);

        Setting::set('commission_tier1_max',  $data['tier1_max']);
        Setting::set('commission_tier1_rate', $data['tier1_rate']);
        Setting::set('commission_tier2_max',  $data['tier2_max']);
        Setting::set('commission_tier2_rate', $data['tier2_rate']);
        Setting::set('commission_tier3_rate', $data['tier3_rate']);

        return redirect()
            ->route('admin.parametres')
            ->with('success', 'Barème de commission mis à jour.');
    }

    /**
     * Numéro + nom du titulaire de la puce, pour chaque opérateur.
     */
    public function updatePaymentAccounts(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (array_keys(Setting::PAYMENT_METHODS) as $key) {
            $rules[$key . '_number'] = ['nullable', 'string', 'max:40'];
            $rules[$key . '_name']   = ['nullable', 'string', 'max:120'];
        }

        $data = $request->validate($rules);

        foreach (array_keys(Setting::PAYMENT_METHODS) as $key) {
            Setting::set('payment_' . $key . '_number', trim($data[$key . '_number'] ?? ''));
            Setting::set('payment_' . $key . '_name',   trim($data[$key . '_name'] ?? ''));
        }

        return redirect()
            ->route('admin.parametres')
            ->with('success', 'Numéros de paiement mis à jour.');
    }

    public function exportNewsletter(): StreamedResponse
    {
        $filename = 'newsletter-abonnes-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Inscrit le']);

            NewsletterSubscriber::orderBy('created_at')
                ->chunk(200, function ($subscribers) use ($handle) {
                    foreach ($subscribers as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->created_at->format('d/m/Y H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroyNewsletterSubscriber(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return redirect()->route('admin.parametres')
            ->with('success', 'Abonné « ' . $email . ' » retiré de la newsletter.');
    }
    
    public function sendNewsletter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'newsletter_subject' => ['required', 'string', 'max:150'],
            'newsletter_message' => ['required', 'string', 'max:5000'],
        ]);

        $emails = NewsletterSubscriber::pluck('email');

        if ($emails->isEmpty()) {
            return redirect()->route('admin.parametres')
                ->with('error', "Aucun abonné à qui envoyer pour l'instant.");
        }

        foreach ($emails->chunk(50) as $lot) {
            Mail::to(config('mail.from.address'))
                ->bcc($lot->all())
                ->send(new NewsletterMail($data['newsletter_subject'], $data['newsletter_message']));
        }

        return redirect()->route('admin.parametres')
            ->with('success', "Email envoyé à {$emails->count()} abonné(s).");
    }
}