<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Catégories disponibles pour un livre.
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

    protected function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'prix_achat' => ['nullable', 'integer', 'min:0'],
            'prix_location' => ['nullable', 'integer', 'min:0'],
            'categorie' => ['required', Rule::in(self::CATEGORIES)],
            'etat' => ['required', Rule::in(['neuf', 'occasion'])],
            'livraison_disponible' => ['nullable', 'boolean'],
            'frais_livraison' => ['nullable', 'integer', 'min:0'],
            // 4096 Ko = 4 Mo
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSeller(), 403, "Seuls les vendeurs peuvent ajouter un livre.");

        $data = $request->validate($this->rules());
        $livraison = $request->boolean('livraison_disponible');

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Nécessite `php artisan storage:link`
            $imagePath = $request->file('image')->store('livres', 'public');
        }

        $request->user()->books()->create([
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'image_path' => $imagePath,
            'livraison_disponible' => $livraison,
            'frais_livraison' => $livraison ? ($data['frais_livraison'] ?? null) : null,
        ]);

        return redirect()->route('profile')->with('success', 'Livre ajouté avec succès.');
    }

    public function edit(Request $request, Book $book): View
    {
        abort_unless($book->seller_id === $request->user()->id, 403);

        return view('books.edit', ['book' => $book]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->seller_id === $request->user()->id, 403);

        $data = $request->validate($this->rules());
        $livraison = $request->boolean('livraison_disponible');

        if ($request->hasFile('image')) {
            $book->image_path = $request->file('image')->store('livres', 'public');
        }

        $book->fill([
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'livraison_disponible' => $livraison,
            'frais_livraison' => $livraison ? ($data['frais_livraison'] ?? null) : null,
        ])->save();

        return redirect()->route('profile')->with('success', 'Livre mis à jour.');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->seller_id === $request->user()->id, 403);

        $book->delete();

        return redirect()->route('profile')->with('success', 'Livre supprimé.');
    }
}