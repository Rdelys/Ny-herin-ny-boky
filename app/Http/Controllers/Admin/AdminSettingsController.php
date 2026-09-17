<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    /** Suggestions rapides affichées comme boutons sur la page. */
    public const QUICK_RATES = [5, 8, 10, 12, 15];

    public function edit(): View
    {
        return view('admin.parametres', [
            'commissionRate' => Setting::commissionRate(),
            'paymentAccounts' => Setting::paymentAccounts(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Setting::set('commission_rate', $data['commission_rate']);

        return redirect()
            ->route('admin.parametres')
            ->with('success', 'Taux de commission mis à jour : ' . $data['commission_rate'] . '%.');
    }

    /**
     * Numéro + nom du titulaire de la puce, pour chaque opérateur.
     * Ces informations sont affichées au client dans la modal de commande :
     * il doit voir à quel nom il envoie l'argent avant de payer.
     */
    public function updatePaymentAccounts(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (array_keys(Setting::PAYMENT_METHODS) as $key) {
            $rules[$key . '_number'] = ['nullable', 'string', 'max:40'];
            $rules[$key . '_name'] = ['nullable', 'string', 'max:120'];
        }

        $data = $request->validate($rules);

        foreach (array_keys(Setting::PAYMENT_METHODS) as $key) {
            Setting::set('payment_' . $key . '_number', trim($data[$key . '_number'] ?? ''));
            Setting::set('payment_' . $key . '_name', trim($data[$key . '_name'] ?? ''));
        }

        return redirect()
            ->route('admin.parametres')
            ->with('success', 'Numéros de paiement mis à jour.');
    }
}
