<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deliverer;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Menu « Commandes » du back-office : toutes les commandes passées par les
 * clients, avec la référence de paiement à vérifier et le suivi de
 * livraison. L'admin est le SEUL à pouvoir changer le statut.
 *
 * Le reversement de l'argent au vendeur est géré à part, dans
 * AdminPaymentController (menu « Paiements »).
 */
class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->query('statut');
        $statutActif = in_array($statut, Order::STATUTS, true) ? $statut : null;

        $orders = Order::query()
            ->with(['buyer', 'seller.sellerProfile', 'deliverer'])
            ->when($statutActif, fn ($q) => $q->where('statut', $statutActif))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.commandes', [
            'orders' => $orders,
            'statutActif' => $statutActif,
            'compteurs' => Order::query()
                ->selectRaw('statut, count(*) as total')
                ->groupBy('statut')
                ->pluck('total', 'statut'),
            'livreurs' => Deliverer::actifs()->orderBy('nom')->get(),
        ]);
    }

    /** Changement de statut (+ assignation d'un livreur si « En livraison »). */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', Rule::in(Order::STATUTS)],
            'deliverer_id' => ['nullable', 'integer', 'exists:deliverers,id'],
        ]);

        // Passer « en livraison » sans livreur n'aurait pas de sens : le
        // client et le vendeur doivent voir qui livre, avec son numéro.
        if ($data['statut'] === Order::STATUT_EN_LIVRAISON && empty($data['deliverer_id'])) {
            throw ValidationException::withMessages([
                'deliverer_id' => 'Choisissez le livreur assigné à cette commande.',
            ]);
        }

        $order->statut = $data['statut'];

        if (in_array($data['statut'], [Order::STATUT_EN_LIVRAISON, 'livree'], true)) {
            // On garde le livreur déjà assigné si l'admin n'en choisit pas
            // un autre en passant la commande à « Livrée ».
            $order->deliverer_id = ($data['deliverer_id'] ?? null) ?: $order->deliverer_id;
        } else {
            $order->deliverer_id = null;
        }

        $order->livree_at = $data['statut'] === 'livree' ? now() : null;
        $order->save();

        return back()->with('success', 'Commande ' . $order->reference . ' : ' . $order->statut_label . '.');
    }
}
