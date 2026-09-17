@extends('layouts.app')

@section('meta_title', __('home.profile_seller_title') . ' — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.profile_seller_title') }}</h2>
                    <p>{{ $profile->nom_entreprise ?? $user->name }}</p>
                </div>
            </div>

            @include('partials.commission-sticker')


            @if(session('success'))
                <p class="flash-success">{{ session('success') }}</p>
            @endif

            <div class="seller-card" style="background:#fffdf7; border:1px solid rgba(85,16,29,.09); color: var(--ink); flex-direction: column; align-items: flex-start; gap: 18px; padding: 30px; margin-bottom: 40px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div class="user-avatar" style="width:56px; height:56px; font-size:1.3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="seller-name" style="color: var(--ink);">{{ $user->name }}</h3>
                        <p class="seller-meta" style="color:#7a6a5d;">{{ $user->email }}</p>
                    </div>
                </div>

                @if($profile)
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; width:100%;">
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_location') }}</p>
                            <p>{{ $profile->localisation ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_postal_code') }}</p>
                            <p>{{ $profile->code_postal ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_payment_number') }}</p>
                            <p>{{ $profile->numero_paiement ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_payment_mode') }}</p>
                            <p>
                                 {{ $profile->mode_paiement === 'abonnement' ? __('home.profile_payment_subscription') : __('home.profile_payment_commission') }}
                                @if($profile->mode_paiement !== 'abonnement')
                                    <span class="book-tag" style="position:static; display:inline-block; margin-left:6px;">{{ rtrim(rtrim(number_format(\App\Models\Setting::commissionRate(), 2, ',', ' '), '0'), ',') }}%</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ AJOUTER UN LIVRE ============ --}}
            <div class="add-book-card">
                <h3 class="add-book-title">{{ __('home.book_add_title') }}</h3>

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

            <div class="section-head">
                <div>
                    <h2>{{ __('home.book_my_books_title') }}</h2>
                    <p>{{ $books->total() }} {{ __('home.book_count_suffix') }}</p>
                </div>
            </div>

            @if($books->isEmpty())
                <p style="color:#7a6a5d;">{{ __('home.book_none_yet') }}</p>
            @else
                <div class="table-scroll">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('home.book_col_title') }}</th>
                                <th>{{ __('home.book_col_category') }}</th>
                                <th>{{ __('home.book_col_price') }}</th>
                                <th>{{ __('home.book_col_quantity') }}</th>
                                <th>{{ __('home.book_col_shipping') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                                <tr class="seller-table-row" onclick="window.location='{{ route('books.edit', $book) }}'">
                                    <td class="seller-table-thumb">
                                        @if($book->image_path)
                                            <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->titre }}" loading="lazy">
                                        @else
                                            <div class="seller-table-thumb-empty"></div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $book->titre }}</strong>
                                        @if($book->auteur)<br><span class="seller-table-sub">{{ $book->auteur }}</span>@endif
                                    </td>
                                    <td><span class="book-genre" style="margin:0;">{{ $book->categorie }}</span></td>
                                    <td>{{ $book->prix_achat ? number_format($book->prix_achat, 0, ',', ' ').' Ar' : '—' }}</td>
                                    <td>{{ $book->quantite }}</td>
                                    <td>
                                        @if($book->livraison_disponible)
                                            <span class="book-tag" style="position:static;">{{ __('home.book_shipping_yes') }}</span>
                                        @else
                                            <span style="color:#96897d;">{{ __('home.book_shipping_no') }}</span>
                                        @endif
                                    </td>
                                    <td class="seller-table-actions" onclick="event.stopPropagation();">
                                        <a href="{{ route('books.edit', $book) }}" class="table-action-link">{{ __('home.book_edit_link') }}</a>
                                        <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('{{ __('home.book_confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="table-action-link table-action-danger">{{ __('home.book_delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($books->hasPages())
                    <div class="pager">
                        @if($books->onFirstPage())
                            <span class="pager-btn disabled">&larr; {{ __('home.pager_previous') }}</span>
                        @else
                            <a href="{{ $books->previousPageUrl() }}" class="pager-btn">&larr; {{ __('home.pager_previous') }}</a>
                        @endif
                        <span class="pager-info">{{ __('home.pager_page_of') }} {{ $books->currentPage() }} / {{ $books->lastPage() }}</span>
                        @if($books->hasMorePages())
                            <a href="{{ $books->nextPageUrl() }}" class="pager-btn">{{ __('home.pager_next') }} &rarr;</a>
                        @else
                            <span class="pager-btn disabled">{{ __('home.pager_next') }} &rarr;</span>
                        @endif
                    </div>
                @endif
            @endif
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