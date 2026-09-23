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
        'Business & entrepreneuriat',
        'Développement personnel',
        'Psychologie',
        'Finance',
        'Marketing',
        'Vente',
        'Communication',
        'Investissement',
        'Roman',
        'Thriller',
        'Science-fiction',
        'Science & non-fiction',
        'Livres malgaches',
        'Éducation financière',
    ];

    /**
     * États possibles d'un livre (clé technique => libellé traduit).
     */
    public const CONDITIONS = ['neuf', 'tres_bon_etat', 'bon_etat'];

    protected function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'prix_achat' => ['nullable', 'integer', 'min:0'],
            'prix_location' => ['nullable', 'integer', 'min:0'],
            'quantite' => ['required', 'integer', 'min:1'],
            'categorie' => ['required', Rule::in(self::CATEGORIES)],
            'etat' => ['required', Rule::in(self::CONDITIONS)],
            'livraison_disponible' => ['nullable', 'boolean'],
            // Pas de frais de livraison dans l'application : aucune validation dessus.
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSeller(), 403, "Seuls les vendeurs peuvent ajouter un livre.");

        $data = $request->validate($this->rules());

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('livres', 'public');
        }

        $request->user()->books()->create([
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'quantite' => $data['quantite'],
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'image_path' => $imagePath,
            'livraison_disponible' => $request->boolean('livraison_disponible'),
        ]);

        return redirect()
            ->route('profile', ['tab' => 'livres'])
            ->with('success', 'Livre ajouté avec succès.');
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

        if ($request->hasFile('image')) {
            $book->image_path = $request->file('image')->store('livres', 'public');
        }

        $book->fill([
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'quantite' => $data['quantite'],
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'livraison_disponible' => $request->boolean('livraison_disponible'),
        ])->save();

        return redirect()
            ->route('profile', ['tab' => 'livres'])
            ->with('success', 'Livre mis à jour.');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->seller_id === $request->user()->id, 403);

        $book->delete();

        return redirect()
            ->route('profile', ['tab' => 'livres'])
            ->with('success', 'Livre supprimé.');
    }
}