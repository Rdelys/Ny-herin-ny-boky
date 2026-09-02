<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Catégories disponibles pour un livre (correspond au menu déroulant
     * "Catégorie" de l'assistant d'ajout côté vendeur).
     */
    public const CATEGORIES = [
        'Business & Entrepreneuriat',
        'Développement personnel',
        'Psychologie',
        'Finance & Investissement',
        'Marketing & Vente',
        'Communication & Leadership',
        'Boky Malagasy',
        'Science & Technologie',
        'Romans',
        'Thriller & Suspense',
        'Autre',
    ];

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSeller(), 403, "Seuls les vendeurs peuvent ajouter un livre.");

        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'prix_achat' => ['nullable', 'integer', 'min:0'],
            'prix_location' => ['nullable', 'integer', 'min:0'],
            'categorie' => ['required', Rule::in(self::CATEGORIES)],
            // 4096 Ko = 4 Mo, comme indiqué dans le formulaire ("Image (4MB)")
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Nécessite `php artisan storage:link` pour être servi via /storage/...
            $imagePath = $request->file('image')->store('livres', 'public');
        }

        $request->user()->books()->create([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'categorie' => $data['categorie'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('profile')->with('success', 'Livre ajouté avec succès.');
    }

    public function destroy(Request $request, \App\Models\Book $book): RedirectResponse
    {
        abort_unless($book->seller_id === $request->user()->id, 403);

        $book->delete();

        return redirect()->route('profile')->with('success', 'Livre supprimé.');
    }
}