<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Affiche le profil : vue "vendeur" ou "client" selon le rôle de l'utilisateur connecté.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        if ($user->isSeller()) {
            // Tableau de bord vendeur : ce que la plateforme lui doit encore
            // et ce qu'elle lui a déjà envoyé (statut piloté par l'admin
            // depuis /admin/paiements).
            $argent = Order::query()
                ->facturables()
                ->where('seller_id', $user->id)
                ->selectRaw('COALESCE(SUM(montant_vendeur), 0) as total')
                ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as en_attente', [Order::PAIEMENT_DU])
                ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as paye', [Order::PAIEMENT_ENVOYE])
                ->selectRaw('COUNT(*) as commandes')
                ->first();

            return view('profile.seller', [
                'user' => $user,
                'profile' => $user->sellerProfile,
                'books' => $user->books()->latest()->paginate(10),
                // Commandes reçues sur ses livres : statut suivi côté admin,
                // avec le livreur assigné dès que la commande part en livraison.
                'orders' => $user->sales()->with(['buyer', 'deliverer'])->latest()->get(),
                'argentEnAttente' => (int) $argent->en_attente,
                'argentPaye' => (int) $argent->paye,
                'totalCommandes' => (int) $argent->commandes,
                'totalStock' => (int) $user->books()->sum('quantite'),
            ]);
        }

        return view('profile.client', [
            'user' => $user,
            'profile' => $user->buyerProfile,
            'orders' => $user->orders()->with(['seller.sellerProfile', 'deliverer'])->latest()->get(),
        ]);
    }

    /**
     * Mise à jour du profil vendeur depuis l'onglet « Modifier mon profil ».
     * Le mode de paiement (commission / abonnement) n'est pas modifiable
     * ici : l'abonnement n'est pas encore ouvert.
     */
    public function updateSeller(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isSeller(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'nom_entreprise' => ['nullable', 'string', 'max:160'],
            'localisation' => ['nullable', 'string', 'max:160'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'numero_paiement' => ['nullable', 'string', 'max:40'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user->sellerProfile()->updateOrCreate([], [
            'nom_entreprise' => $data['nom_entreprise'],
            'localisation' => $data['localisation'],
            'code_postal' => $data['code_postal'],
            'numero_paiement' => $data['numero_paiement'],
        ]);

        return redirect()
            ->route('profile', ['tab' => 'profil'])
            ->with('success', __('home.profile_updated'));
    }

     public function updateClient(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isClient(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'localisation' => ['nullable', 'string', 'max:160'],
            'motif_inscription' => ['nullable', 'string', 'max:255'],
            'types_livres_recherches' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user->buyerProfile()->updateOrCreate([], [
            'localisation' => $data['localisation'] ?? null,
            'motif_inscription' => $data['motif_inscription'] ?? null,
            'types_livres_recherches' => $data['types_livres_recherches'] ?? null,
        ]);

        return redirect()
            ->route('profile', ['tab' => 'profil'])
            ->with('success', __('home.profile_updated'));
    }

     public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('profile', ['tab' => 'profil'])
            ->with('success', __('home.profile_password_updated'));
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password_confirm' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->buyerProfile()?->delete();
        $user->sellerProfile()?->delete();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('home.profile_account_deleted'));
    }

}
