<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        'Business',
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

    public const FORMATS = ['poche', 'broche', 'relie'];

    /** Langues disponibles pour un livre, dans l'ordre de priorité. */
    public const LANGUES = ['mg', 'fr', 'en', 'zh', 'it', 'de', 'es'];

    /** États possibles d'un livre (clé technique => libellé traduit). */
    public const CONDITIONS = ['neuf', 'tres_bon_etat', 'bon_etat'];

    protected function rules(): array
    {
        // Plafond du délai selon l'unité choisie (168 h ou 60 jours).
        $delaiMax = request('delai_livraison_unite') === Book::DELAI_UNITE_HEURES
            ? Book::DELAI_MAX_HEURES
            : Book::DELAI_MAX_JOURS;

        return [
            'titre' => ['required', 'string', 'max:255'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'prix_achat' => ['nullable', 'integer', 'min:0'],
            'prix_location' => ['nullable', 'integer', 'min:0'],
            'promo_type' => ['nullable', Rule::in([Book::PROMO_PERCENT, Book::PROMO_AMOUNT])],
            'promo_valeur' => ['required_if:promo_type,percent,amount', 'nullable', 'integer', 'min:1'],
            'quantite' => ['required', 'integer', 'min:1'],
            'categorie' => ['required', Rule::in(self::CATEGORIES)],
            'etat' => ['required', Rule::in(self::CONDITIONS)],
            'langue' => ['required', Rule::in(self::LANGUES)],
            'format' => ['required', Rule::in(self::FORMATS)],
            'nombre_pages' => ['nullable', 'integer', 'min:1', 'max:20000'],
            'livraison_disponible' => ['nullable', 'boolean'],
            'delai_livraison_unite' => ['required', Rule::in(Book::DELAI_UNITES)],
            'delai_livraison_min' => ['required', 'integer', 'min:1', 'max:' . $delaiMax],
            'delai_livraison_max' => ['required', 'integer', 'min:1', 'max:' . $delaiMax, 'gte:delai_livraison_min'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    /**
     * Contrôles métier de la promotion + valeurs à enregistrer.
     * Sans type => promotion supprimée.
     */
    protected function promoAttributes(array $data): array
    {
        $type = $data['promo_type'] ?? null;

        if (! $type) {
            return ['promo_type' => null, 'promo_valeur' => null];
        }

        $valeur = (int) ($data['promo_valeur'] ?? 0);
        $prix = (int) ($data['prix_achat'] ?? 0);

        if ($prix <= 0) {
            throw ValidationException::withMessages([
                'promo_valeur' => __('home.book_promo_error_no_price'),
            ]);
        }

        if ($type === Book::PROMO_PERCENT && $valeur > Book::PROMO_MAX_PERCENT) {
            throw ValidationException::withMessages([
                'promo_valeur' => __('home.book_promo_error_percent', ['max' => Book::PROMO_MAX_PERCENT]),
            ]);
        }

        if ($type === Book::PROMO_AMOUNT && $valeur >= $prix) {
            throw ValidationException::withMessages([
                'promo_valeur' => __('home.book_promo_error_amount'),
            ]);
        }

        return ['promo_type' => $type, 'promo_valeur' => $valeur];
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSeller(), 403, "Seuls les vendeurs peuvent ajouter un livre.");

        $data = $request->validate($this->rules());
        $promo = $this->promoAttributes($data);

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
            'promo_type' => $promo['promo_type'],
            'promo_valeur' => $promo['promo_valeur'],
            'quantite' => $data['quantite'],
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'langue' => $data['langue'],
            'format' => $data['format'],
            'nombre_pages' => $data['nombre_pages'] ?? null,
            'image_path' => $imagePath,
            'livraison_disponible' => $request->boolean('livraison_disponible'),
            'delai_livraison_min' => $data['delai_livraison_min'],
            'delai_livraison_max' => $data['delai_livraison_max'],
            'delai_livraison_unite' => $data['delai_livraison_unite'],
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
        $promo = $this->promoAttributes($data);

        if ($request->hasFile('image')) {
            $book->image_path = $request->file('image')->store('livres', 'public');
        }

        $book->fill([
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? null,
            'prix_location' => $data['prix_location'] ?? null,
            'promo_type' => $promo['promo_type'],
            'promo_valeur' => $promo['promo_valeur'],
            'quantite' => $data['quantite'],
            'categorie' => $data['categorie'],
            'etat' => $data['etat'],
            'langue' => $data['langue'],
            'format' => $data['format'],
            'nombre_pages' => $data['nombre_pages'] ?? null,
            'livraison_disponible' => $request->boolean('livraison_disponible'),
            'delai_livraison_min' => $data['delai_livraison_min'],
            'delai_livraison_max' => $data['delai_livraison_max'],
            'delai_livraison_unite' => $data['delai_livraison_unite'],
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