<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Enregistre une commande passée depuis la modal (partials/order-modal).
     * Le client a déjà payé via mobile money : il saisit la référence reçue
     * par SMS, que l'admin vérifiera depuis /admin/paiements.
     *
     * La commande démarre TOUJOURS en « en attente de livraison » ; seul
     * l'admin peut ensuite la faire évoluer.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isClient(), 403);

        $data = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'mode_paiement' => ['required', Rule::in(array_keys(Setting::PAYMENT_METHODS))],
            'reference_paiement' => ['required', 'string', 'max:80'],
        ]);

        $book = Book::with('seller')->findOrFail($data['book_id']);

        if ($book->prix_achat === null) {
            return redirect()->route('profile')
                ->with('error', __('home.order_error_not_for_sale'));
        }

        if ($data['quantite'] > $book->quantite) {
            return redirect()->route('profile')
                ->with('error', __('home.order_error_stock', ['quantite' => $book->quantite]));
        }

        $rate = Setting::commissionRate();
        $prixUnitaire = (int) $book->prix_achat_client;

        $order = Order::create([
            'reference' => Order::genererReference(),
            'buyer_id' => $user->id,
            'seller_id' => $book->seller_id,
            'book_id' => $book->id,
            'book_titre' => $book->titre,
            'quantite' => $data['quantite'],
            'prix_unitaire' => $prixUnitaire,
            'total' => $prixUnitaire * $data['quantite'],
            'commission_rate' => $rate,
            'montant_vendeur' => (int) $book->prix_achat * $data['quantite'],
            'mode_paiement' => $data['mode_paiement'],
            'reference_paiement' => $data['reference_paiement'],
            'statut' => Order::STATUT_DEFAUT,
        ]);

        // Décrémente le stock immédiatement à la commande : la quantité est
        // réservée pour éviter toute sur-vente. Une fois à 0, le livre passe
        // automatiquement en « rupture de stock » côté affichage.
        $book->decrement('quantite', $data['quantite']);

        return redirect()->route('profile')
            ->with('success', __('home.order_success', ['reference' => $order->reference]));
    }
}
