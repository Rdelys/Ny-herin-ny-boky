<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Commandes côté acheteur uniquement. Même logique que
 * App\Http\Controllers\OrderController (site web) : la commande démarre
 * toujours "en attente de livraison", seul l'admin la fait évoluer ensuite.
 */
class OrderController extends Controller
{
    /** GET /api/orders — historique des commandes de l'utilisateur connecté. */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['seller.sellerProfile', 'deliverer'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders->map(fn (Order $order) => $this->formatOrder($order)),
        ]);
    }

    /** GET /api/orders/{order} */
    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);

        $order->load(['seller.sellerProfile', 'deliverer']);

        return response()->json(['data' => $this->formatOrder($order)]);
    }

    /** POST /api/orders — passer une commande. */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isClient(), 403, "Seuls les comptes acheteurs peuvent passer commande.");

        $data = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'mode_paiement' => ['required', Rule::in(array_keys(Setting::PAYMENT_METHODS))],
            'reference_paiement' => ['required', 'string', 'max:80'],
        ]);

        $book = Book::with('seller')->findOrFail($data['book_id']);

        if ($book->prix_achat === null) {
            throw ValidationException::withMessages([
                'book_id' => [__('home.order_error_not_for_sale')],
            ]);
        }

        if ($data['quantite'] > $book->quantite) {
            throw ValidationException::withMessages([
                'quantite' => [__('home.order_error_stock', ['quantite' => $book->quantite])],
            ]);
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

        // Stock réservé immédiatement, comme sur le site web.
        $book->decrement('quantite', $data['quantite']);

        return response()->json([
            'data' => $this->formatOrder($order->fresh(['seller.sellerProfile', 'deliverer'])),
            'message' => __('home.order_success', ['reference' => $order->reference]),
        ], 201);
    }

    protected function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'book_titre' => $order->book_titre,
            'quantite' => $order->quantite,
            'prix_unitaire' => $order->prix_unitaire,
            'total' => $order->total,
            'mode_paiement' => $order->mode_paiement,
            'mode_paiement_label' => $order->mode_paiement_label,
            'statut' => $order->statut,
            'statut_label' => $order->statut_traduit,
            'seller' => $order->seller ? [
                'id' => $order->seller->id,
                'nom' => $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name,
            ] : null,
            'deliverer' => $order->deliverer ? [
                'nom' => $order->deliverer->nom,
                'telephone' => $order->deliverer->telephone,
            ] : null,
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }
}
