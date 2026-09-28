<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Menu « Paiements » du back-office : reversement de l'argent de la
 * plateforme VERS LES VENDEURS. Pour chaque commande non annulée, le
 * vendeur a droit à `montant_vendeur` ; l'admin bascule la ligne de
 * « Dû » à « Envoyé » une fois le transfert mobile money effectué.
 *
 * Le suivi des commandes (statut de livraison, livreur) est géré à part,
 * dans AdminOrderController (menu « Commandes »).
 */
class AdminPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->query('statut');
        $statutActif = in_array($statut, array_keys(Order::PAIEMENT_LABELS), true) ? $statut : null;

        $vendeurId = $request->query('vendeur');
        $vendeurActif = User::where('role', 'vendeur')->find($vendeurId);

        $orders = Order::query()
            ->facturables()
            ->with(['seller.sellerProfile', 'buyer'])
            ->when($statutActif, fn ($q) => $q->where('paiement_vendeur', $statutActif))
            ->when($vendeurActif, fn ($q) => $q->where('seller_id', $vendeurActif->id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Récapitulatif par vendeur : c'est le montant que l'admin doit
        // réellement transférer, vendeur par vendeur.
        $parVendeur = Order::query()
            ->facturables()
            ->selectRaw('seller_id')
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as du', [Order::PAIEMENT_DU])
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as envoye', [Order::PAIEMENT_ENVOYE])
            ->selectRaw('COUNT(*) as commandes')
            ->groupBy('seller_id')
            ->with('seller.sellerProfile')
            ->get()
            ->sortByDesc('du')
            ->values();

        return view('admin.paiements', [
            'orders' => $orders,
            'statutActif' => $statutActif,
            'vendeurActif' => $vendeurActif,
            'parVendeur' => $parVendeur,
            'totalDu' => (int) $parVendeur->sum('du'),
            'totalEnvoye' => (int) $parVendeur->sum('envoye'),
            'compteurs' => Order::query()
                ->facturables()
                ->selectRaw('paiement_vendeur, count(*) as total')
                ->groupBy('paiement_vendeur')
                ->pluck('total', 'paiement_vendeur'),
            'vendeurs' => User::where('role', 'vendeur')->with('sellerProfile')->orderBy('name')->get(),
        ]);
    }

    /** Bascule une ligne entre « Dû » et « Envoyé ». */
    public function updatePayout(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'paiement_vendeur' => ['required', Rule::in(array_keys(Order::PAIEMENT_LABELS))],
        ]);

        $order->paiement_vendeur = $data['paiement_vendeur'];
        $order->paiement_vendeur_at = $data['paiement_vendeur'] === Order::PAIEMENT_ENVOYE ? now() : null;
        $order->save();

        return back()->with(
            'success',
            'Commande ' . $order->reference . ' : paiement vendeur marqué « ' . $order->paiement_vendeur_label . ' ».'
        );
    }
}
