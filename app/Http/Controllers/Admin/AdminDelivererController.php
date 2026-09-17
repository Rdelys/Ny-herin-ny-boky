<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deliverer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion de la liste des livreurs (menu « Livreurs » du back-office).
 * C'est dans cette liste que l'admin pioche quand il passe une commande
 * au statut « En livraison ».
 */
class AdminDelivererController extends Controller
{
    public function index(): View
    {
        return view('admin.livreurs.index', [
            'livreurs' => Deliverer::withCount('orders')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'telephone' => ['required', 'string', 'max:40'],
            'zone' => ['nullable', 'string', 'max:120'],
        ]);

        Deliverer::create($data + ['actif' => true]);

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Livreur « ' . $data['nom'] . ' » ajouté.');
    }

    public function update(Request $request, Deliverer $livreur): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'telephone' => ['required', 'string', 'max:40'],
            'zone' => ['nullable', 'string', 'max:120'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $livreur->update([
            'nom' => $data['nom'],
            'telephone' => $data['telephone'],
            'zone' => $data['zone'] ?? null,
            'actif' => (bool) ($data['actif'] ?? false),
        ]);

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Livreur « ' . $livreur->nom . ' » mis à jour.');
    }

    public function destroy(Deliverer $livreur): RedirectResponse
    {
        // Les commandes déjà assignées gardent leur historique : la clé
        // étrangère passe à NULL (nullOnDelete), rien n'est supprimé.
        $nom = $livreur->nom;
        $livreur->delete();

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Livreur « ' . $nom . ' » supprimé.');
    }
}
