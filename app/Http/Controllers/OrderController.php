<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Setting;
use App\Services\InvoiceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user && $user->isSeller(), 403);

        $rules = [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'ville' => ['required', Rule::in(Setting::VILLES)],
            'mode_paiement' => ['required', Rule::in(array_keys(Setting::PAYMENT_METHODS))],
            'reference_paiement' => ['required_unless:mode_paiement,especes', 'nullable', 'string', 'max:80'],
            // Adresse : demandée à TOUT le monde désormais.
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

        $book = Book::with('seller')->findOrFail($data['book_id']);

        if ($book->prix_achat === null) {
            return back()->with('error', __('home.order_error_not_for_sale'));
        }

        if ($data['quantite'] > $book->quantite) {
            return back()->with('error', __('home.order_error_stock', ['quantite' => $book->quantite]));
        }

        $rate = Setting::commissionRateFor($book->prix_achat);
        $prixUnitaire = (int) $book->prix_achat_client;

        $order = Order::create([
            'reference' => Order::genererReference(),
            'buyer_id' => $user?->id,
            'guest_name' => $user ? null : $data['guest_name'],
            'guest_phone' => $user ? null : $data['guest_phone'],
            'guest_email' => $user ? null : ($data['guest_email'] ?? null),
            'adresse_livraison' => $data['adresse_livraison'],
            'seller_id' => $book->seller_id,
            'book_id' => $book->id,
            'book_titre' => $book->titre,
            'quantite' => $data['quantite'],
            'prix_unitaire' => $prixUnitaire,
            'total' => $prixUnitaire * $data['quantite'],
            'commission_rate' => $rate,
            'montant_vendeur' => (int) $book->prix_achat * $data['quantite'],
            'mode_paiement' => $data['mode_paiement'],
            'reference_paiement' => $data['reference_paiement'] ?? __('home.order_payment_cash'),
            'ville' => $data['ville'],
            'statut' => Order::STATUT_DEFAUT,
        ]);

        $book->decrement('quantite', $data['quantite']);

        // Facture générée automatiquement, dès la commande créée.
        InvoiceGenerator::generate($order->fresh(['buyer', 'seller.sellerProfile']));

        if ($user) {
            return redirect()->route('profile')
                ->with('success', __('home.order_success', ['reference' => $order->reference]));
        }

        return back()->with('guest_order_success', [
            'reference' => $order->reference,
            'message' => __('home.order_success', ['reference' => $order->reference]),
            'invoice_url' => \Storage::disk('public')->url($order->facture_path),
        ]);
    }
}