<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Cart;
use App\Services\DeliveryService;
use App\Services\InvoiceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Validation du panier : une Order par livre (chaque ligne a son vendeur,
     * son reversement, son statut) + UNE livraison pour tout le panier.
     */
    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $user = $request->user();

        abort_if($user && $user->isSeller(), 403);

        $rules = [
            'ville' => ['required', Rule::exists('delivery_zones', 'nom')->where('actif', true)],
            'livraison_type' => ['required', Rule::in([DeliveryService::TYPE_STANDARD, DeliveryService::TYPE_VIP])],
            'quartier_id' => ['nullable', 'string', 'max:20'],
            'quartier_autre' => ['nullable', 'string', 'max:120'],
            'cooperative_id' => ['nullable', 'string', 'max:20'],
            'cooperative_autre' => ['nullable', 'string', 'max:120'],
            'heure_prevue' => ['nullable', 'date_format:Y-m-d H:i'],
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
            return back()
                ->withErrors(['mode_paiement' => "Le paiement en espèces n'est disponible qu'à Antananarivo."])
                ->withInput();
        }

        $requested = $cart->raw();

        if (empty($requested)) {
            return redirect()->route('cart.index')->with('error', __('home.cart_empty'));
        }

        try {
            $orders = DB::transaction(function () use ($requested, $data, $user) {
                $groupe = Order::genererGroupeReference();
                $books = Book::with('seller')
                    ->whereIn('id', array_keys($requested))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Livraison : validée et chiffrée côté serveur (ValidationException => retour au formulaire).
                Delivery::create(
                    ['groupe_reference' => $groupe] + DeliveryService::resolve($data, $books->values())
                );

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
                        'reference' => Order::genererReference($groupe, $created->count() + 1),
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
            return redirect()->route('cart.index')->withInput()->with('error', $e->getMessage());
        }

        $groupe = $orders->first()->groupe_reference;

        // Panier vidé AVANT la facture : si elle échoue, la commande ne peut
        // jamais être renvoyée deux fois.
        $cart->clear();

        try {
            InvoiceGenerator::generate($orders->first());
        } catch (\Throwable $e) {
            report($e);
        }

        $factureUrl = $orders->first()->fresh()->facture_url;

        $message = __('home.order_success', ['reference' => $groupe]);

        if ($user) {
            return redirect()->route('profile')->with('success', $message);
        }

        return redirect()->route('cart.index')->with('guest_order_success', [
            'message' => $message,
            'groupe' => $groupe,
            'invoice_url' => $factureUrl,
            'orders' => $orders->map(fn (Order $o) => [
                'reference' => $o->reference,
                'titre' => $o->book_titre,
            ])->all(),
        ]);
    }
}