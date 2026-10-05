<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryCooperative;
use App\Models\DeliveryQuartier;
use App\Models\DeliveryZone;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menu « Livraison » : règles & tarif VIP, provinces, quartiers d'Antananarivo,
 * coopératives, et validation des quartiers / coopératives saisis par les clients.
 */
class AdminDeliveryController extends Controller
{
    public function index(): View
    {
        $dedupe = fn ($d) => $d->zone_id . '|' . mb_strtolower((string) ($d->quartier_nom ?? $d->cooperative_nom));

        return view('admin.livraison', [
            'settings' => Setting::deliverySettings(),
            'zones' => DeliveryZone::withCount(['quartiers', 'cooperatives'])->orderBy('position')->orderBy('nom')->get(),
            'quartiers' => DeliveryQuartier::orderBy('nom')->get(),
            'cooperatives' => DeliveryCooperative::with('zone')->orderBy('zone_id')->orderBy('nom')->get(),
            'villesProvinces' => DeliveryZone::where('est_capitale', false)->orderBy('nom')->get(),
            'pendingQuartiers' => Delivery::whereNull('quartier_id')->where('quartier_personnalise', true)->latest()->get()->unique($dedupe),
            'pendingCoops' => Delivery::whereNull('cooperative_id')->where('cooperative_personnalisee', true)->latest()->get()->unique($dedupe),
        ]);
    }

    // ---------------- règles & VIP ----------------
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vip_min_h' => ['required', 'integer', 'min:1', 'max:48'],
            'vip_max_h' => ['required', 'integer', 'gt:vip_min_h', 'max:72'],
            'vip_surcharge' => ['required', 'integer', 'min:0'],
            'vip_open_hour' => ['required', 'integer', 'between:0,23'],
            'vip_close_hour' => ['required', 'integer', 'gt:vip_open_hour', 'max:24'],
            'vip_max_lead_h' => ['required', 'integer', 'min:1', 'max:720'],
            'standard_max_h' => ['required', 'integer', 'min:1', 'max:720'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set('delivery_' . $key, $value);
        }
        Setting::set('delivery_vip_actif', $request->boolean('vip_actif') ? 1 : 0);

        return back()->with('success', 'Règles de livraison mises à jour.');
    }

    // ---------------- provinces ----------------
    public function storeZone(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120', 'unique:delivery_zones,nom'],
            'frais' => ['required', 'integer', 'min:0'],
            'delai_min_h' => ['required', 'integer', 'min:1'],
            'delai_max_h' => ['required', 'integer', 'gte:delai_min_h'],
        ]);

        DeliveryZone::create($data + ['actif' => true, 'position' => (int) DeliveryZone::max('position') + 1]);

        return back()->with('success', 'Province « ' . $data['nom'] . ' » ajoutée.');
    }

    public function updateZone(Request $request, DeliveryZone $zone): RedirectResponse
    {
        $data = $request->validate([
            'frais' => ['nullable', 'integer', 'min:0'],
            'delai_min_h' => ['required', 'integer', 'min:1'],
            'delai_max_h' => ['required', 'integer', 'gte:delai_min_h'],
        ]);

        $zone->update([
            'frais' => $zone->est_capitale ? 0 : (int) ($data['frais'] ?? 0),
            'delai_min_h' => $data['delai_min_h'],
            'delai_max_h' => $data['delai_max_h'],
            'actif' => $zone->est_capitale ? true : $request->boolean('actif'),
        ]);

        return back()->with('success', 'Province « ' . $zone->nom . ' » mise à jour.');
    }

    // ---------------- quartiers (Antananarivo) ----------------
    public function storeQuartier(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'frais' => ['required', 'integer', 'min:0'],
        ]);

        $tana = DeliveryZone::where('est_capitale', true)->firstOrFail();
        DeliveryQuartier::updateOrCreate(
            ['zone_id' => $tana->id, 'nom' => trim($data['nom'])],
            ['frais' => $data['frais'], 'actif' => true]
        );

        return back()->with('success', 'Quartier « ' . $data['nom'] . ' » enregistré.');
    }

    public function updateQuartier(Request $request, DeliveryQuartier $quartier): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'frais' => ['required', 'integer', 'min:0'],
        ]);

        $quartier->update($data + ['actif' => $request->boolean('actif')]);

        return back()->with('success', 'Quartier « ' . $quartier->nom . ' » mis à jour.');
    }

    public function destroyQuartier(DeliveryQuartier $quartier): RedirectResponse
    {
        $quartier->delete(); // les livraisons passées gardent leur copie du nom et du prix

        return back()->with('success', 'Quartier supprimé.');
    }

    // ---------------- coopératives ----------------
    public function storeCooperative(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:delivery_zones,id'],
            'nom' => ['required', 'string', 'max:120'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:190'],
        ]);

        DeliveryCooperative::updateOrCreate(
            ['zone_id' => $data['zone_id'], 'nom' => trim($data['nom'])],
            ['telephone' => $data['telephone'] ?? null, 'note' => $data['note'] ?? null, 'actif' => true]
        );

        return back()->with('success', 'Coopérative « ' . $data['nom'] . ' » enregistrée.');
    }

    public function updateCooperative(Request $request, DeliveryCooperative $cooperative): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:190'],
        ]);

        $cooperative->update($data + ['actif' => $request->boolean('actif')]);

        return back()->with('success', 'Coopérative « ' . $cooperative->nom . ' » mise à jour.');
    }

    public function destroyCooperative(DeliveryCooperative $cooperative): RedirectResponse
    {
        $cooperative->delete();

        return back()->with('success', 'Coopérative supprimée.');
    }

    // ---------------- validation des saisies « Autre » ----------------

    /** Ajoute le quartier saisi par un client à la liste, avec son prix, et met à jour les livraisons concernées. */
    public function approveQuartier(Request $request, Delivery $delivery): RedirectResponse
    {
        $data = $request->validate(['frais' => ['required', 'integer', 'min:0']]);

        $quartier = DeliveryQuartier::updateOrCreate(
            ['zone_id' => $delivery->zone_id, 'nom' => $delivery->quartier_nom],
            ['frais' => $data['frais'], 'actif' => true]
        );

        Delivery::whereNull('quartier_id')
            ->where('quartier_personnalise', true)
            ->where('zone_id', $delivery->zone_id)
            ->whereRaw('LOWER(quartier_nom) = ?', [mb_strtolower($delivery->quartier_nom)])
            ->get()
            ->each(function (Delivery $d) use ($quartier, $data) {
                $d->quartier_id = $quartier->id;
                $this->applyFee($d, $data['frais']);
            });

        return back()->with('success', 'Quartier « ' . $quartier->nom . ' » ajouté à la liste.');
    }

    public function approveCooperative(Request $request, Delivery $delivery): RedirectResponse
    {
        $data = $request->validate(['telephone' => ['nullable', 'string', 'max:40']]);

        $coop = DeliveryCooperative::firstOrCreate(
            ['zone_id' => $delivery->zone_id, 'nom' => $delivery->cooperative_nom],
            ['telephone' => $data['telephone'] ?? null, 'actif' => true]
        );

        Delivery::whereNull('cooperative_id')
            ->where('cooperative_personnalisee', true)
            ->where('zone_id', $delivery->zone_id)
            ->whereRaw('LOWER(cooperative_nom) = ?', [mb_strtolower($delivery->cooperative_nom)])
            ->update(['cooperative_id' => $coop->id]);

        return back()->with('success', 'Coopérative « ' . $coop->nom . ' » ajoutée à la liste.');
    }

    /** Fixe le tarif d'UNE livraison (quartier saisi par le client), sans toucher à la liste. */
    public function setFee(Request $request, Delivery $delivery): RedirectResponse
    {
        $data = $request->validate(['frais' => ['required', 'integer', 'min:0']]);

        $this->applyFee($delivery, $data['frais']);

        return back()->with('success', 'Frais de livraison fixés pour ' . $delivery->groupe_reference . '.');
    }

    private function applyFee(Delivery $d, int $base): void
    {
        if (! $d->frais_gratuit) {
            $d->frais_base = $base;
            $d->frais = $base + (int) $d->supplement_vip;
        }
        $d->frais_a_confirmer = false;
        $d->save();
    }
}