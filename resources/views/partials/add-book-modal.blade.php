{{-- ============ MODAL "AJOUTER UN LIVRE" ============ --}}
{{-- Déclenchée par le bouton #openAddBookModalBtn (voir le <script> en bas
     de profile/seller.blade.php). Se rouvre automatiquement s'il y a une
     erreur de validation sur l'un de ses champs, sinon les erreurs
     resteraient invisibles derrière un modal fermé. --}}

<div class="modal-overlay" id="addBookModalOverlay">
    <div class="modal-panel modal-panel--wide" id="addBookModalPanel" role="dialog" aria-modal="true" aria-labelledby="addBookModalTitle">
        <button type="button" class="modal-close" id="addBookModalClose" aria-label="Fermer">&times;</button>

        <h2 class="modal-title" id="addBookModalTitle">{{ __('home.book_add_title') }}</h2>

        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="modal-form">
            @csrf

            <div class="modal-form-row">
                <label>{{ __('home.book_title_label') }}
                    <input type="text" name="titre" placeholder="{{ __('home.book_title_placeholder') }}" value="{{ old('titre') }}" required>
                </label>
                <label>{{ __('home.book_author_label') }}
                    <input type="text" name="auteur" placeholder="{{ __('home.book_author_placeholder') }}" value="{{ old('auteur') }}">
                </label>
            </div>
            @error('titre')<p class="modal-field-error">{{ $message }}</p>@enderror

            <label>{{ __('home.book_description_label') }}
                <textarea name="description" rows="3" placeholder="{{ __('home.book_description_placeholder') }}">{{ old('description') }}</textarea>
            </label>

            <div class="modal-form-row">
                <label>{{ __('home.book_purchase_price') }}
                    <input type="number" name="prix_achat" min="0" value="{{ old('prix_achat') }}" id="prixAchatInput">
                </label>
                <label>{{ __('home.book_quantity') }}
                    <input type="number" name="quantite" min="1" value="{{ old('quantite', 1) }}" required>
                </label>
            </div>
            @error('quantite')<p class="modal-field-error">{{ $message }}</p>@enderror
            <p class="field-hint" id="prixAchatClientHint"></p>

            <label>{{ __('home.book_rental_price') }}
                <input type="number" name="prix_location" min="0" value="{{ old('prix_location') }}" id="prixLocationInput">
            </label>
            <p class="field-hint" id="prixLocationClientHint"></p>

            <div class="modal-form-row">
                <label>{{ __('home.book_category') }}
                    <select name="categorie" required>
                        <option value="">{{ __('home.auth_choose_placeholder') }}</option>
                        @foreach(\App\Http\Controllers\BookController::CATEGORIES as $cat)
                            <option value="{{ $cat }}" @selected(old('categorie') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </label>
                <label>{{ __('home.book_condition') }}
                    <select name="etat" required>
                        <option value="neuf" @selected(old('etat') === 'neuf')>{{ __('home.book_condition_neuf') }}</option>
                        <option value="tres_bon_etat" @selected(old('etat', 'bon_etat') === 'tres_bon_etat')>{{ __('home.book_condition_tres_bon_etat') }}</option>
                        <option value="bon_etat" @selected(old('etat', 'bon_etat') === 'bon_etat')>{{ __('home.book_condition_bon_etat') }}</option>
                    </select>
                </label>
            </div>
            @error('categorie')<p class="modal-field-error">{{ $message }}</p>@enderror

            <fieldset class="modal-fieldset">
                <legend>{{ __('home.book_shipping_legend') }}</legend>
                <label class="modal-radio-card">
                    <input type="checkbox" name="livraison_disponible" value="1" {{ old('livraison_disponible') ? 'checked' : '' }}>
                    <span>
                        <strong>{{ __('home.book_shipping_available') }}</strong>
                        <small>{{ __('home.book_shipping_desc') }}</small>
                    </span>
                </label>
            </fieldset>

            <label>{{ __('home.book_image_label') }}
                <input type="file" name="image" accept="image/*">
            </label>
            <p class="field-hint">{{ __('home.book_image_hint') }}</p>
            @error('image')<p class="modal-field-error">{{ $message }}</p>@enderror

            <button type="submit" class="btn-modal-primary">{{ __('home.book_submit_add') }}</button>
        </form>
    </div>
</div>