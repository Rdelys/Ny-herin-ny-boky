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

    // Le formulaire d'ajout de livre vit maintenant dans un modal : s'il y a
    // une erreur de validation sur l'un de ses champs, il faut le rouvrir
    // automatiquement au chargement, sinon l'erreur reste invisible derrière
    // un modal fermé.
    $addBookHasError = $errors->hasAny(['titre', 'auteur', 'description', 'prix_achat', 'prix_location', 'quantite', 'categorie', 'etat', 'image']);
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
                                    <span class="field-hint" style="display:inline; margin:0 0 0 6px;">(selon le prix de chaque livre — voir nos CGV)</span>
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

                {{-- ============ AJOUT : commandes annulées ============ --}}
                <div class="profile-stat-card profile-stat-card-cancelled">
                    <span class="profile-stat-label">{{ __('home.profile_orders_cancelled') }}</span>
                    <strong class="profile-stat-value">{{ $commandesAnnulees }}</strong>
                    <span class="profile-stat-sub">{{ __('home.profile_orders_cancelled_hint') }}</span>
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
                <div class="section-head">
                    <div>
                        <h2>{{ __('home.book_my_books_title') }}</h2>
                        <p>{{ $books->total() }} {{ __('home.book_count_suffix') }}</p>
                    </div>
                    <button type="button" class="btn-modal-primary" id="openAddBookModalBtn" style="white-space:nowrap;">
                        {{ __('home.book_add_title') }}
                    </button>
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

    @include('partials.add-book-modal')

    <script>
        (function(){
            var COMMISSION_TIERS = @json(
                collect(\App\Models\Setting::commissionTiers())->map(function ($t) {
                    return ['max' => $t['max'], 'rate' => $t['rate'] / 100];
                })->values()
            );

            function commissionRateFor(montant) {
                montant = Number(montant) || 0;
                for (var i = 0; i < COMMISSION_TIERS.length; i++) {
                    var t = COMMISSION_TIERS[i];
                    if (t.max === null || montant <= t.max) return t.rate;
                }
                return COMMISSION_TIERS[COMMISSION_TIERS.length - 1].rate;
            }

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
                    var rate = commissionRateFor(value);
                    hint.textContent = label + ' ' + formatAr(value * (1 + rate));
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

            // ---- modal "Ajouter un livre" ----
            var addBookOverlay = document.getElementById('addBookModalOverlay');
            var addBookClose = document.getElementById('addBookModalClose');
            var openAddBookBtn = document.getElementById('openAddBookModalBtn');

            function openAddBookModal(){
                if (!addBookOverlay) return;
                addBookOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            function closeAddBookModal(){
                if (!addBookOverlay) return;
                addBookOverlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            if (openAddBookBtn) openAddBookBtn.addEventListener('click', openAddBookModal);
            if (addBookClose) addBookClose.addEventListener('click', closeAddBookModal);
            if (addBookOverlay) {
                addBookOverlay.addEventListener('click', function(e){
                    if (e.target === addBookOverlay) closeAddBookModal();
                });
            }
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && addBookOverlay && addBookOverlay.classList.contains('open')) {
                    closeAddBookModal();
                }
            });

            @if($addBookHasError)
                openAddBookModal();
            @endif
        })();
    </script>
@endsection