<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;

/**
 * Panier stocké en session (invités et clients). On ne garde que
 * [book_id => quantité] : prix et stock sont toujours relus en base.
 */
class Cart
{
    private const KEY = 'panier';

    /** @return array<int,int> book_id => quantité */
    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    public function has(int $bookId): bool
    {
        return isset($this->raw()[$bookId]);
    }

    /** Cumule la quantité, plafonnée au stock. Retourne la quantité présente dans le panier. */
    public function add(Book $book, int $quantite): int
    {
        $items = $this->raw();
        $items[$book->id] = min(($items[$book->id] ?? 0) + $quantite, $book->quantite);
        session([self::KEY => $items]);

        return $items[$book->id];
    }

    public function set(Book $book, int $quantite): void
    {
        $items = $this->raw();
        $items[$book->id] = max(1, min($quantite, $book->quantite));
        session([self::KEY => $items]);
    }

    public function remove(int $bookId): void
    {
        $items = $this->raw();
        unset($items[$bookId]);
        session([self::KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** Lignes prêtes à afficher. Les livres supprimés entre-temps sont retirés du panier. */
    public function lines(): Collection
    {
        $items = $this->raw();

        if (! $items) {
            return collect();
        }

        $books = Book::with('seller.sellerProfile')
            ->whereIn('id', array_keys($items))
            ->get()
            ->keyBy('id');

        foreach (array_keys($items) as $id) {
            if (! $books->has($id)) {
                $this->remove($id);
            }
        }

        return $books->map(function (Book $book) use ($items) {
            $quantite = $items[$book->id];
            $prix = (int) $book->prix_achat_client;

            return (object) [
                'book' => $book,
                'quantite' => $quantite,
                'prix_unitaire' => $prix,
                'total' => $prix * $quantite,
                'disponible' => $book->prix_achat !== null && $quantite <= $book->quantite,
            ];
        })->values();
    }

    public function total(Collection $lines): int
    {
        return (int) $lines->sum('total');
    }
}