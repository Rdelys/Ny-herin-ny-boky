@extends('layouts.app')

@section('meta_title', __('home.book_edit_title') . ' « ' . $book->titre . ' » — ' . config('app.name'))
@section('meta_robots', 'noindex, nofollow')

@section('content')
    <section>
        <div class="wrap" style="max-width: 640px;">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.book_edit_title') }}</h2>
                    <p>{{ $book->titre }}</p>
                </div>
                <a href="{{ route('profile') }}" class="see-all">&larr; {{ __('home.book_back_to_profile') }}</a>
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
                        <label>{{ __('home.book_title_label') }}
                            <input type="text" name="titre" value="{{ old('titre', $book->titre) }}" required>
                        </label>
                        <label>{{ __('home.book_author_label') }}
                            <input type="text" name="auteur" value="{{ old('auteur', $book->auteur) }}">
                        </label>
                    </div>

                    <label>{{ __('home.book_description_label') }}
                        <textarea name="description" rows="3">{{ old('description', $book->description) }}</textarea>
                    </label>

                    <div class="modal-form-row">
                        <label>{{ __('home.book_purchase_price') }}
                            <input type="number" name="prix_achat" min="0" value="{{ old('prix_achat', $book->prix_achat) }}" id="prixAchatInput">
                        </label>
                        <label>{{ __('home.book_quantity') }}
                            <input type="number" name="quantite" min="1" value="{{ old('quantite', $book->quantite) }}" required>
                        </label>
                    </div>
                    @error('quantite')<p class="modal-field-error">{{ $message }}</p>@enderror
                    <p class="field-hint" id="prixAchatClientHint"></p>

                    <label>{{ __('home.book_rental_price') }}
                        <input type="number" name="prix_location" min="0" value="{{ old('prix_location', $book->prix_location) }}" id="prixLocationInput">
                    </label>
                    <p class="field-hint" id="prixLocationClientHint"></p>

                    <div class="modal-form-row">
                        <label>{{ __('home.book_category') }}
                            <select name="categorie" required>
                                @foreach(\App\Http\Controllers\BookController::CATEGORIES as $cat)
                                    <option value="{{ $cat }}" @selected(old('categorie', $book->categorie) === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>{{ __('home.book_condition') }}
                            <select name="etat" required>
                                <option value="neuf" @selected(old('etat', $book->etat) === 'neuf')>{{ __('home.book_condition_neuf') }}</option>
                                <option value="tres_bon_etat" @selected(old('etat', $book->etat) === 'tres_bon_etat')>{{ __('home.book_condition_tres_bon_etat') }}</option>
                                <option value="bon_etat" @selected(old('etat', $book->etat) === 'bon_etat')>{{ __('home.book_condition_bon_etat') }}</option>
                            </select>
                        </label>
                    </div>

                    <fieldset class="modal-fieldset">
                        <legend>{{ __('home.book_shipping_legend') }}</legend>
                        <label class="modal-radio-card">
                            <input type="checkbox" name="livraison_disponible" value="1" {{ old('livraison_disponible', $book->livraison_disponible) ? 'checked' : '' }}>
                            <span>
                                <strong>{{ __('home.book_shipping_available') }}</strong>
                                <small>{{ __('home.book_shipping_desc') }}</small>
                            </span>
                        </label>
                    </fieldset>

                    <label>{{ __('home.book_image_replace') }}
                        <input type="file" name="image" accept="image/*">
                    </label>
                    <p class="field-hint">{{ __('home.book_image_keep_hint') }}</p>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn-modal-primary" style="flex:2;">{{ __('home.book_submit_save') }}</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('{{ __('home.book_confirm_delete') }}');" style="margin-top:14px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="table-action-link table-action-danger" style="width:100%; text-align:center; padding:12px; border:1px solid rgba(179,38,30,.3); border-radius:999px;">
                        {{ __('home.book_delete_permanent') }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function(){
            var COMMISSION_RATE = 0.10;

            function formatAr(n){
                return Math.round(n).toLocaleString('fr-FR') + ' Ar';
            }

            function bindPriceHint(inputId, hintId, label){
                var input = document.getElementById(inputId);
                var hint = document.getElementById(hintId);
                if (!input || !hint) return;

                function update(){
                    var value = parseFloat(input.value);
                    if (!value || value <= 0) {
                        hint.textContent = '';
                        return;
                    }
                    hint.textContent = label + ' ' + formatAr(value * (1 + COMMISSION_RATE));
                }

                input.addEventListener('input', update);
                update();
            }

            bindPriceHint('prixAchatInput', 'prixAchatClientHint', '{{ __('home.book_client_price_hint') }}');
            bindPriceHint('prixLocationInput', 'prixLocationClientHint', '{{ __('home.book_client_price_hint') }}');
        })();
    </script>
@endsection