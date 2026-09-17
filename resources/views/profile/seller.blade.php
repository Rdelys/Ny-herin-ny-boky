@extends('layouts.app')

@section('meta_title', __('home.profile_seller_title') . ' — ' . config('app.name'))
@section('meta_robots', 'noindex, nofollow')

@php
    // Onglet ouvert au chargement. Les liens de pagination et les
    // redirections de validation repassent par ?tab=... pour rouvrir le
    // bon panneau au lieu de retomber sur les commandes.
    $tabActif = in_array(request('tab'), ['commandes', 'livres', 'profil'], true)
        ? request('tab')
        : 'commandes';

    $books->appends(['tab' => 'livres']);
@endphp

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
            @if(session('error'))
                <p class="flash-error">{{ session('error') }}</p>
            @endif

            {{-- ============ IDENTITÉ ============ --}}
            <div class="profile-identity">
                <div class="profile-identity-head">
                    <div class="user-avatar profile-identity-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="seller-name" style="color: var(--ink);">{{ $user->name }}</h3>
                        <p class="seller-meta" style="color:#7a6a5d;">{{ $user->email }}</p>
                    </div>
                </div>

                @if($profile)
                    <div class="profile-info-grid">
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

            {{-- ============ TABLEAU DE BORD ============ --}}
            <h3 class="profile-section-title">{{ __('home.profile_dashboard_title') }}</h3>

            <div class="profile-stat-grid">
                <div class="profile-stat-card profile-stat-card-pending">
                    <span class="profile-stat-label">{{ __('home.profile_money_pending') }}</span>
                    <strong class="profile-stat-value">{{ number_format($argentEnAttente, 0, ',', ' ') }} Ar</strong>
                    <span class="profile-stat-sub">{{ __('home.profile_money_pending_hint') }}</span>
                </div>
                <div class="profile-stat-card profile-stat-card-paid">
                    <span class="profile-stat-label">{{ __('home.profile_money_paid') }}</span>
                    <strong class="profile-stat-value">{{ number_format($argentPaye, 0, ',', ' ') }} Ar</strong>
                    <span class="profile-stat-sub">{{ __('home.profile_money_paid_hint') }}</span>
                </div>
                <div class="profile-stat-card">
                    <span class="profile-stat-label">{{ __('home.profile_orders_count') }}</span>
                    <strong class="profile-stat-value">{{ $totalCommandes }}</strong>
                </div>
                <div class="profile-stat-card">
                    <span class="profile-stat-label">{{ __('home.book_my_books_title') }}</span>
                    <strong class="profile-stat-value">{{ $books->total() }}</strong>
                    <span class="profile-stat-sub">{{ $totalStock }} {{ __('home.profile_stock_count') }}</span>
                </div>
            </div>

            {{-- ============ ONGLETS ============ --}}
            <div class="profile-tabs" role="tablist">
                <button type="button" class="profile-tab {{ $tabActif === 'commandes' ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $tabActif === 'commandes' ? 'true' : 'false' }}"
                        aria-controls="tab-commandes" data-profile-tab="commandes">
                    {{ __('home.profile_tab_orders') }}
                    <span class="profile-tab-count">{{ $totalCommandes }}</span>
                </button>
                <button type="button" class="profile-tab {{ $tabActif === 'livres' ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $tabActif === 'livres' ? 'true' : 'false' }}"
                        aria-controls="tab-livres" data-profile-tab="livres">
                    {{ __('home.profile_tab_books') }}
                    <span class="profile-tab-count">{{ $books->total() }}</span>
                </button>
                <button type="button" class="profile-tab {{ $tabActif === 'profil' ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $tabActif === 'profil' ? 'true' : 'false' }}"
                        aria-controls="tab-profil" data-profile-tab="profil">
                    {{ __('home.profile_tab_edit') }}
                </button>
            </div>

            {{-- ---------- onglet 1 : commandes reçues ---------- --}}
            <div class="profile-panel {{ $tabActif === 'commandes' ? 'active' : '' }}" id="tab-commandes" role="tabpanel">
                @include('partials.orders-table', ['orders' => $orders, 'role' => 'seller'])
            </div>

            {{-- ---------- onglet 2 : mes livres ---------- --}}
            <div class="profile-panel {{ $tabActif === 'livres' ? 'active' : '' }}" id="tab-livres" role="tabpanel">
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
                                        <td>
                                            @if($book->quantite <= 0)
                                                <span class="book-tag" style="position:static; background:rgba(179,38,30,.92); color:#fff;">{{ __('home.books_out_of_stock') }}</span>
                                            @else
                                                {{ $book->quantite }}
                                            @endif
                                        </td>
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

            {{-- ---------- onglet 3 : modifier mon profil ---------- --}}
            <div class="profile-panel {{ $tabActif === 'profil' ? 'active' : '' }}" id="tab-profil" role="tabpanel">
                <div class="add-book-card">
                    <h3 class="add-book-title">{{ __('home.profile_edit_title') }}</h3>
                    <p class="field-hint" style="margin:-10px 0 18px;">{{ __('home.profile_edit_intro') }}</p>

                    <form method="POST" action="{{ route('profile.seller.update') }}" class="modal-form">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-row">
                            <label>{{ __('home.profile_name_label') }}
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                            </label>
                            <label>{{ __('home.profile_email_label') }}
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </label>
                        </div>
                        @error('name')<p class="modal-field-error">{{ $message }}</p>@enderror
                        @error('email')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <label>{{ __('home.profile_company_label') }}
                            <input type="text" name="nom_entreprise" value="{{ old('nom_entreprise', $profile->nom_entreprise ?? '') }}">
                        </label>
                        @error('nom_entreprise')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <div class="modal-form-row">
                            <label>{{ __('home.profile_location') }}
                                <input type="text" name="localisation" value="{{ old('localisation', $profile->localisation ?? '') }}" placeholder="Antananarivo, Fianarantsoa...">
                            </label>
                            <label>{{ __('home.profile_postal_code') }}
                                <input type="text" name="code_postal" value="{{ old('code_postal', $profile->code_postal ?? '') }}">
                            </label>
                        </div>
                        @error('localisation')<p class="modal-field-error">{{ $message }}</p>@enderror
                        @error('code_postal')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <label>{{ __('home.profile_payment_number') }}
                            <input type="text" name="numero_paiement" value="{{ old('numero_paiement', $profile->numero_paiement ?? '') }}" placeholder="034 xx xxx xx">
                        </label>
                        <p class="field-hint">{{ __('home.profile_payment_number_hint') }}</p>
                        @error('numero_paiement')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <button type="submit" class="btn-modal-primary">{{ __('home.profile_save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function(){
            var COMMISSION_RATE = {{ \App\Models\Setting::commissionRate() / 100 }};

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

            // ---- onglets : commandes / livres / profil ----
            // L'onglet ouvert est aussi écrit dans l'URL (?tab=...) : la
            // pagination des livres et les erreurs de validation
            // rouvrent ainsi le bon panneau.
            var tabs = document.querySelectorAll('[data-profile-tab]');

            tabs.forEach(function(tab){
                tab.addEventListener('click', function(){
                    var cible = tab.getAttribute('data-profile-tab');

                    tabs.forEach(function(autre){
                        var actif = autre === tab;
                        autre.classList.toggle('active', actif);
                        autre.setAttribute('aria-selected', actif ? 'true' : 'false');
                    });

                    document.querySelectorAll('.profile-panel').forEach(function(panel){
                        panel.classList.toggle('active', panel.id === 'tab-' + cible);
                    });

                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', cible);
                    url.searchParams.delete('page');
                    window.history.replaceState({}, '', url);
                });
            });
        })();
    </script>
@endsection
