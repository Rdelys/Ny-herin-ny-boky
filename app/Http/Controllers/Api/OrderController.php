<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Setting;
use App\Services\InvoiceGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/**
 * Commandes côté acheteur. Même logique que App\Http\Controllers\OrderController
 * (site web) : la commande démarre toujours "en attente de livraison", seul
 * l'admin la fait évoluer ensuite. store() est accessible sans compte (achat
 * invité), exactement comme sur le site — index()/show() restent réservées
 * aux comptes connectés (middleware auth:sanctum + client.api sur la route).
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

    /**
     * POST /api/orders — passer une commande.
     *
     * Route publique (voir routes/api.php) : si un jeton Sanctum valide est
     * fourni, l'acheteur est identifié normalement ; sinon la commande est
     * traitée comme un achat invité, avec les mêmes champs guest_* que sur
     * le site web.
     */
    public function store(Request $request): JsonResponse
{
    $user = Auth::guard('sanctum')->user();

    if ($user && $user->isSeller()) {
        return response()->json(['message' => __('home.order_seller_cant_order')], 403);
    }

    $rules = [
        'items' => ['required', 'array', 'min:1'],
        'items.*.book_id' => ['required', 'integer', 'distinct', 'exists:books,id'],
        'items.*.quantite' => ['required', 'integer', 'min:1'],
        'ville' => ['required', Rule::in(Setting::VILLES)],
        'mode_paiement' => ['required', Rule::in(array_keys(Setting::PAYMENT_METHODS))],
        'reference_paiement' => ['required_unless:mode_paiement,especes', 'nullable', 'string', 'max:80'],
        'adresse_livraison' => ['required', 'string', 'max:255'],
    ];

    if (! $user) {
        $rules['guest_name'] = ['required', 'string', 'max:120'];
        $rules['guest_phone'] = ['required', 'string', 'max:30'];
        $rules['guest_email'] = ['nullable', 'email', 'max:190'];
    }

    $data = $request->validate($rules);

    if ($data['mode_paiement'] === 'especes' && $data['ville'] !== Setting::VILLE_ESPECES) {
        throw ValidationException::withMessages([
            'mode_paiement' => ["Le paiement en espèces n'est disponible qu'à Antananarivo."],
        ]);
    }

    $requested = collect($data['items'])->pluck('quantite', 'book_id')->all();
    $groupe = Order::genererGroupeReference();

    try {
        $orders = DB::transaction(function () use ($requested, $data, $user, $groupe) {
            $books = Book::with('seller')
                ->whereIn('id', array_keys($requested))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $created = collect();

            foreach ($requested as $bookId => $quantite) {
                $book = $books->get($bookId);

                if (! $book || $book->prix_achat === null) {
                    throw new \DomainException(($book ? $book->titre . ' : ' : '') . __('home.order_error_not_for_sale'));
                }
                if ($quantite > $book->quantite) {
                    throw new \DomainException($book->titre . ' : ' . __('home.order_error_stock', ['quantite' => $book->quantite]));
                }

                $rate = Setting::commissionRateFor($book->prix_achat);
                $prixUnitaire = (int) $book->prix_achat_client;

                $created->push(Order::create([
                    'reference' => Order::genererReference(),
                    'groupe_reference' => $groupe,
                    'buyer_id' => $user?->id,
                    'guest_name' => $user ? null : $data['guest_name'],
                    'guest_phone' => $user ? null : $data['guest_phone'],
                    'guest_email' => $user ? null : ($data['guest_email'] ?? null),
                    'adresse_livraison' => $data['adresse_livraison'],
                    'seller_id' => $book->seller_id,
                    'book_id' => $book->id,
                    'book_titre' => $book->titre,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total' => $prixUnitaire * $quantite,
                    'commission_rate' => $rate,
                    'montant_vendeur' => (int) $book->prix_achat * $quantite,
                    'mode_paiement' => $data['mode_paiement'],
                    'reference_paiement' => $data['reference_paiement'] ?? __('home.order_payment_cash'),
                    'ville' => $data['ville'],
                    'statut' => Order::STATUT_DEFAUT,
                ]));

                $book->decrement('quantite', $quantite);
            }

            return $created;
        });
    } catch (\DomainException $e) {
        throw ValidationException::withMessages(['items' => [$e->getMessage()]]);
    }

    // Une seule facture pour tout le panier (comme le site).
    try {
        InvoiceGenerator::generate($orders->first());
    } catch (\Throwable $e) {
        report($e);
    }

    return response()->json([
        'data' => $orders->map(fn (Order $o) => $this->formatOrder($o->fresh(['seller.sellerProfile', 'deliverer']))),
        'groupe_reference' => $groupe,
        'message' => __('home.order_success', ['reference' => $groupe]),
        'invoice_url' => $orders->first()->fresh()->facture_url,
    ], 201);
}

    protected function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
'reference' => $order->reference,
'groupe_reference' => $order->groupe_reference,
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
            'facture_url' => $order->facture_url,
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }
}