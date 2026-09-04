@extends('layouts.app')

@section('meta_title', 'Modifier « ' . $book->titre . ' » — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap" style="max-width: 640px;">
            <div class="section-head">
                <div>
                    <h2>Modifier le livre</h2>
                    <p>{{ $book->titre }}</p>
                </div>
                <a href="{{ route('profile') }}" class="see-all">&larr; Retour à mon espace</a>
            </div>

            <div class="add-book-card" style="max-width:none;">
                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="modal-form">
                    @csrf
                    @method('PUT')

                    @if($book->image_path)
                        <div class="book-cover" style="max-width:180px; border-radius:14px;">
                            <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->titre }}">
                        </div>
                    @endif

                    <div class="modal-form-row">
                        <label>Titre
                            <input type="text" name="titre" value="{{ old('titre', $book->titre) }}" required>
                        </label>
                        <label>Auteur
                            <input type="text" name="auteur" value="{{ old('auteur', $book->auteur) }}">
                        </label>
                    </div>

                    <label>Description
                        <textarea name="description" rows="3">{{ old('description', $book->description) }}</textarea>
                    </label>

                    <div class="modal-form-row">
                        <label>Prix d'achat (Ar)
                            <input type="number" name="prix_achat" min="0" value="{{ old('prix_achat', $book->prix_achat) }}">
                        </label>
                        <label>Prix de location (Ar)
                            <input type="number" name="prix_location" min="0" value="{{ old('prix_location', $book->prix_location) }}">
                        </label>
                    </div>

                    <div class="modal-form-row">
                        <label>Catégorie
                            <select name="categorie" required>
                                @foreach(\App\Http\Controllers\BookController::CATEGORIES as $cat)
                                    <option value="{{ $cat }}" @selected(old('categorie', $book->categorie) === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>État
                            <select name="etat" required>
                                <option value="neuf" @selected(old('etat', $book->etat) === 'neuf')>Neuf</option>
                                <option value="occasion" @selected(old('etat', $book->etat) === 'occasion')>Occasion</option>
                            </select>
                        </label>
                    </div>

                    <fieldset class="modal-fieldset">
                        <legend>Livraison</legend>
                        <label class="modal-radio-card">
                            <input type="checkbox" name="livraison_disponible" value="1" id="shippingToggle" {{ old('livraison_disponible', $book->livraison_disponible) ? 'checked' : '' }}>
                            <span>
                                <strong>Livraison disponible</strong>
                                <small>Cochez si vous proposez la livraison pour ce livre</small>
                            </span>
                        </label>
                        <label id="shippingFeeField" style="{{ old('livraison_disponible', $book->livraison_disponible) ? '' : 'display:none;' }}">
                            Frais de livraison (Ar)
                            <input type="number" name="frais_livraison" min="0" placeholder="0 = livraison gratuite" value="{{ old('frais_livraison', $book->frais_livraison) }}">
                        </label>
                    </fieldset>

                    <label>Remplacer l'image
                        <input type="file" name="image" accept="image/*">
                    </label>
                    <p class="field-hint">Laissez vide pour garder l'image actuelle. Image (4 Mo max)</p>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn-modal-primary" style="flex:2;">Enregistrer</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Supprimer définitivement ce livre ?');" style="margin-top:14px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="table-action-link table-action-danger" style="width:100%; text-align:center; padding:12px; border:1px solid rgba(179,38,30,.3); border-radius:999px;">
                        Supprimer ce livre
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function(){
            var toggle = document.getElementById('shippingToggle');
            var feeField = document.getElementById('shippingFeeField');
            if (toggle && feeField) {
                toggle.addEventListener('change', function(){
                    feeField.style.display = toggle.checked ? '' : 'none';
                });
            }
        })();
    </script>
@endsection