<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BookController as WebBookController;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Catalogue en lecture seule pour l'app mobile (acheteurs).
 * Aucune route de création/édition/suppression de livre ici : ça reste
 * une action vendeur, réservée au site web (voir App\Http\Controllers\BookController).
 */
class BookController extends Controller
{
    /**
     * GET /api/books
     * Mêmes filtres que la page /livres du site : q (mot-clé) + categorie.
     */
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $categorie = (string) $request->query('categorie', '');
        $perPage = min((int) $request->query('per_page', 12), 30);

        $books = Book::query()
            ->with(['seller.sellerProfile'])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('titre', 'like', "%{$query}%")
                        ->orWhere('auteur', 'like', "%{$query}%")
                        ->orWhere('categorie', 'like', "%{$query}%");
                });
            })
            ->when($categorie !== '', fn ($q) => $q->where('categorie', $categorie))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => collect($books->items())->map(fn (Book $book) => $this->formatBook($book)),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'total' => $books->total(),
                'per_page' => $books->perPage(),
            ],
        ]);
    }

    /** GET /api/books/{book} */
    public function show(Book $book): JsonResponse
    {
        $book->load('seller.sellerProfile');

        return response()->json(['data' => $this->formatBook($book, true)]);
    }

    /** GET /api/categories — liste fixe utilisée pour les filtres. */
    public function categories(): JsonResponse
    {
        return response()->json(['data' => WebBookController::CATEGORIES]);
    }

    /**
     * GET /api/payment-accounts
     * Numéros mobile money + nom du titulaire configurés par l'admin
     * (Setting::paymentAccounts()), nécessaires pour l'écran de commande.
     * Seuls les opérateurs dont le numéro est renseigné sont renvoyés.
     */
    public function paymentAccounts(): JsonResponse
    {
        $accounts = collect(Setting::paymentAccounts())
            ->filter(fn ($account) => $account['numero'] !== '')
            ->map(fn ($account, $key) => [
                'key' => $key,
                'label' => $account['label'],
                'numero' => $account['numero'],
                'nom' => $account['nom'],
            ])
            ->values();

        return response()->json(['data' => $accounts]);
    }

    protected function formatBook(Book $book, bool $withDetails = false): array
    {
        $seller = $book->seller;

        $data = [
            'id' => $book->id,
            'titre' => $book->titre,
            'auteur' => $book->auteur,
            'categorie' => $book->categorie,
            'etat' => $book->etat,
            'quantite' => $book->quantite,
            'en_stock' => $book->quantite > 0,
            'prix_achat' => $book->prix_achat_client,
            'prix_location' => $book->prix_location_client,
            'livraison_disponible' => (bool) $book->livraison_disponible,
            'image_url' => $book->image_path
                ? asset('storage/' . $book->image_path)
                : 'https://picsum.photos/seed/nhb-book-' . $book->id . '/500/667',
            'seller' => $seller ? [
                'id' => $seller->id,
                'nom' => $seller->sellerProfile->nom_entreprise ?? $seller->name,
                'localisation' => $seller->sellerProfile->localisation ?? null,
            ] : null,
        ];

        if ($withDetails) {
            $data['description'] = $book->description;
        }

        return $data;
    }
}
