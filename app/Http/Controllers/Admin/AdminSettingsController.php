<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.parametres', [
            'tiers'           => Setting::commissionTiers(),
            'paymentAccounts' => Setting::paymentAccounts(),
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
}