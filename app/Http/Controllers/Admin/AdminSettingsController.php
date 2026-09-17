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
}