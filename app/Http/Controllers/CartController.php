<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Setting;
use App\Services\Cart;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('cart.index', [
            'lines' => $lines,
            'total' => $this->cart->total($lines),
            'canCheckout' => $lines->every(fn ($line) => $line->disponible),
            'paymentAccounts' => array_filter(Setting::paymentAccounts(), fn ($a) => $a['numero'] !== ''),
            'delivery' => DeliveryService::config($lines->pluck('book')->values()),
        ]);
    }

    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->isSeller()) {
            return $this->respond($request, false, __('home.order_seller_cant_order'));
        }

        $data = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'quantite' => ['required', 'integer', 'min:1'],
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->prix_achat === null) {
            return $this->respond($request, false, __('home.order_error_not_for_sale'));
        }

        if ($book->quantite <= 0) {
            return $this->respond($request, false, __('home.order_error_stock', ['quantite' => 0]));
        }

        $avant = $this->cart->raw()[$book->id] ?? 0;
        $apres = $this->cart->add($book, $data['quantite']);

        // Plafonné au stock : on prévient au lieu de dire "ajouté".
        if ($apres < $avant + $data['quantite']) {
            return $this->respond($request, true, __('home.order_error_stock', ['quantite' => $book->quantite]));
        }

        return $this->respond($request, true, __('home.cart_added'));
    }

    public function update(Request $request, int $book): RedirectResponse
    {
        $data = $request->validate([
            'quantite' => ['required', 'integer', 'min:1'],
        ]);

        if ($this->cart->has($book)) {
            $this->cart->set(Book::findOrFail($book), $data['quantite']);
        }

        return redirect()->route('cart.index');
    }

    public function destroy(int $book): RedirectResponse
    {
        $this->cart->remove($book);

        return redirect()->route('cart.index');
    }

    /** JSON pour le modal (fetch), redirection classique sinon. */
    private function respond(Request $request, bool $ok, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'count' => $this->cart->count(),
            ], $ok ? 200 : 422);
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }
}