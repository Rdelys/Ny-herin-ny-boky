@extends('layouts.app')

@section('meta_title', __('home.cart_title') . ' — ' . config('app.name'))
@section('meta_robots', 'noindex, nofollow')

@push('styles')
<style>
    .cart-layout{
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
        gap: 28px;
        align-items: start;
    }
    .cart-items{ display: flex; flex-direction: column; gap: 18px; }

    /* ---- ligne = mini fiche produit ---- */
    .cart-item{
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        background: #fffdf7;
        border: 1px solid rgba(85,16,29,.09);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(61,11,21,.05), 0 14px 26px -20px rgba(61,11,21,.3);
    }
    .cart-item.is-unavailable{ border-color: rgba(179,38,30,.4); background: rgba(179,38,30,.03); }
    .cart-item-media{
        position: relative;
        background: var(--cream-dim);
        min-height: 230px;
    }
    .cart-item-media img{ position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    /* étiquette promo : pas de pastille d'état au-dessus ici */
    .cart-item-media .book-promo-tag{ top: 10px; left: 10px; }
    .cart-item-body{ display: flex; flex-direction: column; gap: 8px; padding: 20px 22px; min-width: 0; }
    .cart-item-top{ display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .cart-item-title{
        font-family: var(--serif);
        font-weight: 600;
        font-size: 1.2rem;
        line-height: 1.2;
        color: var(--ink);
        margin: 0;
    }
    .cart-item-author{ margin: 0; font-size: .86rem; font-style: italic; color: #7a6a5d; }
    .cart-item-seller{ margin: 0; font-size: .78rem; font-weight: 600; color: #a8957f; }
    .cart-item-badges{ display: flex; flex-wrap: wrap; gap: 6px; }
    .cart-item-badges .book-genre{ margin: 0; font-size: .64rem; }
    .cart-item-price{
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 4px 8px;
        font-family: var(--serif);
        font-size: 1rem;
        color: #7a6a5d;
    }
    .cart-item-old{
        font-size: .82rem;
        color: #9c8b7d;
        text-decoration: line-through;
        text-decoration-thickness: 1.5px;
    }
    .cart-item-promo-badge{
        align-self: center;
        font-family: var(--sans, system-ui, sans-serif);
        font-size: .64rem;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(179,38,30,.12);
        color: #b3261e;
        white-space: nowrap;
    }
    .cart-item-foot{
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px dashed rgba(85,16,29,.14);
    }
    .cart-item-total{ font-family: var(--serif); font-weight: 700; font-size: 1.3rem; color: var(--maroon-800); white-space: nowrap; }
    .cart-item-remove button{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px; height: 34px;
        border-radius: 50%;
        border: 1px solid rgba(179,38,30,.28);
        background: transparent;
        color: #b3261e;
        transition: background .15s ease;
    }
    .cart-item-remove button:hover{ background: rgba(179,38,30,.08); }

    .cart-stepper{ display: flex; align-items: center; border: 1px solid rgba(85,16,29,.18); border-radius: 999px; overflow: hidden; }
    .cart-stepper button{
        width: 36px; height: 36px;
        border: 0;
        background: rgba(85,16,29,.06);
        color: var(--maroon-800);
        font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s ease;
    }
    .cart-stepper button:hover{ background: rgba(85,16,29,.13); }
    .cart-stepper input{
        width: 48px; height: 36px;
        border: 0;
        background: transparent;
        text-align: center;
        font: inherit;
        font-weight: 700;
        -moz-appearance: textfield;
    }
    .cart-stepper input::-webkit-outer-spin-button,
    .cart-stepper input::-webkit-inner-spin-button{ -webkit-appearance: none; margin: 0; }

    /* ---- colonne de droite ---- */
    .cart-aside{ display: flex; flex-direction: column; gap: 18px; }
    .cart-aside .cart-summary{ margin: 0; }
    .cart-aside .add-book-card{ margin: 0; max-width: none; padding: 24px 22px; }
    .cart-savings{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: -6px 0 0;
        padding: 10px 16px;
        border-radius: 12px;
        background: rgba(179,38,30,.07);
        color: #b3261e;
        font-size: .86rem;
        font-weight: 700;
    }

    @media (max-width: 960px){
        .cart-layout{ grid-template-columns: 1fr; }
    }
    @media (max-width: 600px){
        .cart-item{ grid-template-columns: 112px minmax(0, 1fr); border-radius: 16px; }
        .cart-item-media{ min-height: 170px; }
        .cart-item-body{ padding: 14px; gap: 6px; }
        .cart-item-title{ font-size: 1.02rem; }
        .cart-item-total{ font-size: 1.1rem; }
        .cart-aside .add-book-card{ padding: 20px 16px; }
    }
    @media (max-width: 380px){
        .cart-item{ grid-template-columns: 1fr; }
        .cart-item-media{ min-height: 0; height: 210px; }
        .cart-item-media img{ object-fit: contain; }
    }
</style>
@endpush

@section('content')
    @php
        $guestSuccess = session('guest_order_success');

        // Économie totale réalisée grâce aux promotions (prix client).
        $economie = $lines->sum(function ($line) {
            $b = $line->book;
            return $b->en_promo
                ? max(0, $b->prix_achat_client_original - $b->prix_achat_client) * $line->quantite
                : 0;
        });
    @endphp

    <section>
        <div class="wrap" style="max-width: {{ ($guestSuccess || $lines->isEmpty()) ? '860px' : '1120px' }};">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.cart_title') }}</h2>
                    @if(! $guestSuccess && $lines->isNotEmpty())
                        <p>{{ __('home.cart_items_count', ['count' => $lines->sum('quantite')]) }}</p>
                    @endif
                </div>
                <a href="{{ route('books.index') }}" class="see-all">{{ __('home.cart_browse') }}</a>
            </div>

            @if(session('success'))
                <p class="flash-success">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p class="flash-error">{{ session('error') }}</p>
            @endif

            @if($guestSuccess)
                {{-- Confirmation invité (il n'a pas de profil où être redirigé). --}}
                <div class="order-guest-success">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2 class="modal-title" style="margin-top:14px;">{{ __('home.order_guest_success_title') }}</h2>
                    <p>{{ $guestSuccess['message'] }}</p>
                    <p class="order-guest-reference">{{ $guestSuccess['groupe'] }}</p>

                    @if(! empty($guestSuccess['invoice_url']))
                        <a href="{{ $guestSuccess['invoice_url'] }}" target="_blank" class="btn btn-primary" style="margin: 8px auto 0;">
                            {{ __('home.order_download_invoice') }}
                        </a>
                    @endif

                    <div style="margin-top:14px;">
                        @foreach($guestSuccess['orders'] as $o)
                            <span class="seller-table-sub" style="display:block;">{{ $o['titre'] }} · {{ $o['reference'] }}</span>
                        @endforeach
                    </div>

                    <p class="order-static-note">{{ __('home.order_guest_success_hint') }}</p>
                </div>

            @elseif($lines->isEmpty())
                <p style="color:#7a6a5d;">{{ __('home.cart_empty') }}</p>

            @else
                <div class="cart-layout">

                    {{-- ============ LIGNES DU PANIER (fiches produit) ============ --}}
                    <div class="cart-items">
                        @foreach($lines as $line)
                            @php $book = $line->book; @endphp
                            <article class="cart-item {{ $line->disponible ? '' : 'is-unavailable' }}">
                                <div class="cart-item-media">
                                    <img src="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}" alt="{{ $book->titre }}" loading="lazy">

                                    {{-- ============ AJOUT : étiquette promo ============ --}}
                                    @include('partials.book-price', ['book' => $book, 'mode' => 'tag'])
                                </div>

                                <div class="cart-item-body">
                                    <div class="cart-item-top">
                                        <div>
                                            @if($book->categorie)
                                                <span class="book-genre">{{ $book->categorie }}</span>
                                            @endif
                                            <h3 class="cart-item-title">{{ $book->titre }}</h3>
                                        </div>
                                    </div>

                                    @if($book->auteur)
                                        <p class="cart-item-author">{{ $book->auteur }}</p>
                                    @endif
                                    <p class="cart-item-seller">{{ $book->seller->sellerProfile->nom_entreprise ?? $book->seller->name }}</p>

                                    <div class="cart-item-badges">
                                        <span class="book-genre" style="background: rgba(233,178,63,.18); color:#8a5f14;">{{ __('home.book_condition_' . $book->etat) }}</span>
                                        @if($book->langue_label)
                                            <span class="book-genre" style="background: rgba(85,16,29,.08); color: var(--maroon-800);">{{ $book->langue_label }}</span>
                                        @endif
                                        @if($book->format_label)
                                            <span class="book-genre" style="background: rgba(92,138,55,.1); color: var(--green-700);">{{ $book->format_label }}</span>
                                        @endif
                                        @if($book->pages_label)
                                            <span class="book-genre" style="background: rgba(85,16,29,.06); color:#6b5a4d;">{{ $book->pages_label }}</span>
                                        @endif
                                        <span class="book-genre" style="background: rgba(92,138,55,.14); color:#395e26;">{{ $book->delai_livraison_label }}</span>
                                    </div>

                                    {{-- ============ MODIFIÉ : ancien prix barré + badge promo ============ --}}
                                    <span class="cart-item-price">
                                        @if($book->en_promo)
                                            <s class="cart-item-old">{{ number_format($book->prix_achat_client_original, 0, ',', ' ') }} Ar</s>
                                        @endif
                                        <span>{{ number_format($line->prix_unitaire, 0, ',', ' ') }} Ar / {{ __('home.order_unit_price') }}</span>
                                        @if($book->en_promo)
                                            <span class="cart-item-promo-badge">{{ $book->promo_label }}</span>
                                        @endif
                                    </span>

                                    @unless($line->disponible)
                                        <span class="modal-field-error" style="margin:0;">{{ __('home.order_error_stock', ['quantite' => $book->quantite]) }}</span>
                                    @endunless

                                    <div class="cart-item-foot">
                                        <form method="POST" action="{{ route('cart.update', $book->id) }}" class="cart-stepper" data-cart-form>
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" data-cart-step="-1" aria-label="-">&minus;</button>
                                            <input type="number" name="quantite" value="{{ $line->quantite }}" min="1" max="{{ max(1, $book->quantite) }}" inputmode="numeric" aria-label="{{ __('home.order_quantity_label') }}">
                                            <button type="button" data-cart-step="1" aria-label="+">&plus;</button>
                                        </form>

                                        <strong class="cart-item-total">{{ number_format($line->total, 0, ',', ' ') }} Ar</strong>

                                        <form method="POST" action="{{ route('cart.remove', $book->id) }}" class="cart-item-remove">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="{{ __('home.cart_remove') }}" title="{{ __('home.cart_remove') }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- ============ RÉCAP + FINALISER ============ --}}
                    <aside class="cart-aside">
                        <div class="cart-summary">
                            <span>{{ __('home.delivery_subtotal') }}</span>
                            <strong>{{ number_format($total, 0, ',', ' ') }} Ar</strong>
                        </div>

                        {{-- ============ AJOUT : économie réalisée grâce aux promos ============ --}}
                        @if($economie > 0)
                            <p class="cart-savings">
                                <span>{{ __('home.cart_savings_label') }}</span>
                                <span>-{{ number_format($economie, 0, ',', ' ') }} Ar</span>
                            </p>
                        @endif

                        @include('cart._checkout')
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // ---- boutons − / + des quantités du panier ----
    (function(){
        document.querySelectorAll('[data-cart-form]').forEach(function(form){
            var input = form.querySelector('input[name="quantite"]');
            var timer = null;

            function clamp(v){
                var min = parseInt(input.min, 10) || 1;
                var max = parseInt(input.max, 10) || 99;
                return Math.max(min, Math.min(max, v || min));
            }
            function submitLater(){
                clearTimeout(timer);
                timer = setTimeout(function(){ form.submit(); }, 350);
            }

            form.querySelectorAll('[data-cart-step]').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var current = parseInt(input.value, 10) || 1;
                    var next = clamp(current + parseInt(btn.getAttribute('data-cart-step'), 10));
                    if (next === current) return;
                    input.value = next;
                    submitLater();
                });
            });
            input.addEventListener('change', function(){
                input.value = clamp(parseInt(input.value, 10));
                submitLater();
            });
        });
    })();
</script>
@endpush