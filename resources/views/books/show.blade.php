@extends('layouts.app')

@section('meta_title', $book->titre . ' — ' . config('app.name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $book->description) ?: $book->titre, 160))

@php
    $isSeller   = auth()->check() && auth()->user()->isSeller();
    $inStock    = $book->quantite > 0;
    $image      = $book->image_path
        ? asset('storage/'.$book->image_path)
        : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/800/1067';
    $sellerName = $book->seller->sellerProfile->nom_entreprise ?? $book->seller->name;

    // ⚠️ Remplacez par votre vraie source si vous avez une table de quartiers.
    $districts = $districts ?? array_values(array_unique([
        'Analakely', 'Antaninarenina', 'Ambatomitsangana', 'Isoraka', 'Tsaralalàna',
        'Antohomadinika', 'Ankadifotsy', 'Ambohipo', 'Ambohijatovo', 'Andravoahangy',
        'Ankadikely', 'Ivandry', 'Analamahitsy', 'Ankorondrano', 'Ambanidia',
        'Antanimena', 'Mahamasina', 'Ampefiloha', 'Anosy', 'Andoharanofotsy',
        'Itaosy', 'Ankadimbahoaka', 'Anosibe', 'Ambolokandrina', "Andrefan'Ambohijanahary",
        'Ambohipo Ambony', 'Ankatso', 'Ambatobe', 'Soavimasoandro', 'Miandrarivo',
        'Antsahavola', 'Ambodivona', 'Ankorondrano Avaratra', 'Ankadivato', 'Amparibe',
        'Ambatonakanga', 'Amboasarikely', 'Ambohipo Ambany', "Andohan'Analakely", 'Ankazomanga',
        'Anosipatrana', 'Antsobolo', 'Mahazoarivo', 'Ambohimitsimbina',
        'Antanimora', 'Andranomena', 'Ambohibao', 'Ankaditapaka', 'Amboniloha',
    ]));
    sort($districts);
@endphp

@section('content')
<style>
    /* ============================================
       FICHE PRODUIT — DESIGN IMMERSIF & INNOVANT
       ============================================ */
    :root {
        --pp-cream: #faf7f0;
        --pp-cream-2: #f3ecdc;
        --pp-gold-soft: rgba(233,178,63,.14);
        --pp-shadow-lg: 0 30px 60px -30px rgba(61,11,21,.5);
        --pp-shadow-md: 0 16px 36px -20px rgba(61,11,21,.4);
        --pp-radius: 24px;
    }

    .pp-wrap { position: relative; }

    /* ---------- FIL D'ARIANE ---------- */
    .pp-breadcrumb {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        font-size: .82rem; color: #8a7a6d; margin-bottom: 28px;
        padding: 10px 16px; border-radius: 999px;
        background: rgba(250,247,240,.6);
        border: 1px solid rgba(85,16,29,.06);
        width: fit-content;
    }
    .pp-breadcrumb a { color: #6b5a4d; text-decoration: none; transition: color .2s ease; }
    .pp-breadcrumb a:hover { color: var(--maroon-800,#55101d); text-decoration: underline; }
    .pp-breadcrumb .sep { opacity: .45; }
    .pp-breadcrumb .current { color: var(--ink,#2a1a14); font-weight: 600; }

    /* ---------- LAYOUT PRINCIPAL ---------- */
    .pp-main {
        display: grid;
        grid-template-columns: minmax(0,5fr) minmax(0,6fr);
        gap: 52px;
        align-items: start;
    }

    /* ============================================
       GALERIE / MÉDIA
       ============================================ */
    .pp-gallery { position: sticky; top: 96px; }

    .pp-media-frame {
        position: relative;
        border-radius: var(--pp-radius);
        padding: 14px;
        background: linear-gradient(160deg, #ffffff 0%, var(--pp-cream) 55%, var(--pp-cream-2) 100%);
        box-shadow: var(--pp-shadow-lg);
        border: 1px solid rgba(85,16,29,.06);
    }
    .pp-media-frame::before {
        content: '';
        position: absolute;
        inset: 8px;
        border-radius: calc(var(--pp-radius) - 8px);
        border: 1px dashed rgba(233,178,63,.35);
        pointer-events: none;
    }

    .pp-media {
        position: relative;
        aspect-ratio: 4/5;
        overflow: hidden;
        border-radius: calc(var(--pp-radius) - 10px);
        cursor: zoom-in;
        background: linear-gradient(160deg, #f6efdd, #d9c99f);
    }
    /* ✅ Correction de l'étirement : object-fit contain sur fond neutre */
    .pp-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        padding: 10px;
        transition: transform .22s cubic-bezier(.2,.7,.3,1);
        will-change: transform;
        user-select: none;
        -webkit-user-drag: none;
    }
    .pp-media.is-zooming img { transform: scale(2.1); }

    /* Effet de brillance au survol */
    .pp-media::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.22) 45%, transparent 60%);
        transform: translateX(-120%);
        transition: transform .9s ease;
        pointer-events: none;
        z-index: 1;
    }
    .pp-media:hover::after { transform: translateX(120%); }

    .pp-media .book-tag {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 3;
        backdrop-filter: blur(8px);
        box-shadow: 0 6px 16px -8px rgba(0,0,0,.35);
    }

    .pp-zoom-btn {
        position: absolute;
        right: 16px;
        bottom: 16px;
        z-index: 3;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,253,247,.96);
        color: var(--maroon-900,#3d0b15);
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        box-shadow: 0 10px 22px -10px rgba(0,0,0,.5);
        transition: opacity .2s ease, transform .2s ease;
        backdrop-filter: blur(6px);
    }
    .pp-media.is-zooming .pp-zoom-btn { opacity: 0; transform: scale(.8); }

    /* Badge vendeur flottant */
    .pp-seller-float {
        position: absolute;
        left: 16px;
        bottom: 16px;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px 8px 10px;
        border-radius: 999px;
        background: rgba(255,253,247,.96);
        color: var(--ink,#2a1a14);
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 10px 22px -12px rgba(0,0,0,.45);
        backdrop-filter: blur(8px);
        transition: transform .2s ease, color .2s ease;
        max-width: calc(100% - 88px);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .pp-seller-float:hover { transform: translateY(-2px); color: var(--maroon-800,#55101d); }
    .pp-seller-float .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--gold,#e9b23f);
        box-shadow: 0 0 0 3px rgba(233,178,63,.25);
        flex-shrink: 0;
    }

    /* ============================================
       RÉSUMÉ / ACHAT
       ============================================ */
    .pp-summary {
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-width: 0;
    }

    .pp-meta-top {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .pp-seller {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: .82rem;
        font-weight: 600;
        color: #8a7a6d;
        text-decoration: none;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(233,178,63,.1);
        transition: background .2s ease, color .2s ease;
    }
    .pp-seller::before {
        content: '';
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--gold,#e9b23f);
        flex-shrink: 0;
    }
    .pp-seller:hover { color: var(--maroon-800,#55101d); background: rgba(233,178,63,.22); }

    .pp-title {
        font-family: var(--serif,Georgia,serif);
        font-weight: 600;
        font-size: clamp(1.65rem,3vw,2.4rem);
        line-height: 1.12;
        color: var(--ink,#2a1a14);
        margin: 0;
        letter-spacing: -.01em;
    }
    .pp-author {
        margin: -8px 0 0;
        font-size: 1.02rem;
        color: #7a6a5d;
        font-style: italic;
    }

    /* ---------- BLOC PRIX IMMERSIF ---------- */
    .pp-price-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 20px 22px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(61,11,21,.04), rgba(233,178,63,.1));
        border: 1px solid rgba(85,16,29,.08);
        position: relative;
        overflow: hidden;
    }
    .pp-price-card::before {
        content: '';
        position: absolute;
        top: -40%; right: -10%;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(233,178,63,.22), transparent 70%);
        pointer-events: none;
    }
    .pp-price {
        font-family: var(--serif,Georgia,serif);
        font-weight: 700;
        font-size: 2.15rem;
        color: var(--maroon-800,#55101d);
        line-height: 1;
        letter-spacing: -.02em;
    }
    .pp-stock {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8rem;
        font-weight: 700;
        color: #395e26;
        background: rgba(92,138,55,.14);
        padding: 6px 14px;
        border-radius: 999px;
    }
    .pp-stock::before {
        content: '';
        width: 7px; height: 7px; border-radius: 50%;
        background: #5c8a37;
        box-shadow: 0 0 0 3px rgba(92,138,55,.2);
    }
    .pp-stock.is-out { color: #b3261e; background: rgba(179,38,30,.12); }
    .pp-stock.is-out::before { background: #b3261e; box-shadow: 0 0 0 3px rgba(179,38,30,.18); }

    /* ---------- BADGES ---------- */
    .pp-badges { display: flex; flex-wrap: wrap; gap: 8px; }
    .pp-badges .book-genre {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        transition: transform .2s ease;
    }
    .pp-badges .book-genre:hover { transform: translateY(-1px); }

    .pp-short-desc {
        margin: 0;
        font-size: .96rem;
        line-height: 1.75;
        color: #4a3a30;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        padding-left: 14px;
        border-left: 3px solid rgba(233,178,63,.5);
    }

    /* ---------- LIVRAISON ---------- */
    .pp-delivery {
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, rgba(92,138,55,.1), rgba(92,138,55,.04));
        border: 1px solid rgba(92,138,55,.15);
        border-radius: 14px;
        padding: 14px 16px;
        font-size: .88rem;
        color: #395e26;
    }
    .pp-delivery svg { flex-shrink: 0; }
    .pp-delivery span { color: #6b5a4d; }
    .pp-delivery strong { margin-left: auto; text-align: right; }

    /* ---------- ACHAT ---------- */
    .pp-buy { display: flex; flex-direction: column; gap: 18px; }
    .pp-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .pp-label {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #9c8b7d;
    }
    .pp-total {
        font-family: var(--serif,Georgia,serif);
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--maroon-800,#55101d);
    }
    .pp-qty .order-qty-stepper button { width: 42px; height: 42px; font-size: 1.2rem; }
    .pp-qty .order-qty-stepper input { width: 54px; height: 42px; background: transparent; }

    .pp-add {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 17px 22px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--maroon-900,#3d0b15), var(--maroon-800,#55101d));
        color: var(--cream,#f6efdd);
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        box-shadow: 0 14px 28px -14px rgba(85,16,29,.65);
        position: relative;
        overflow: hidden;
    }
    .pp-add::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.18) 45%, transparent 60%);
        transform: translateX(-120%);
        transition: transform .8s ease;
    }
    .pp-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 36px -16px rgba(85,16,29,.75);
        background: linear-gradient(135deg, var(--maroon-800,#55101d), var(--maroon-900,#3d0b15));
    }
    .pp-add:hover::before { transform: translateX(120%); }
    .pp-add:active { transform: translateY(0); }

    .pp-seller-block {
        text-align: center;
        background: linear-gradient(135deg, rgba(233,178,63,.14), rgba(233,178,63,.06));
        border: 1px dashed var(--gold,#e9b23f);
        border-radius: 16px;
        padding: 22px;
        color: #6b5a4d;
        font-size: .92rem;
    }

    /* ---------- QUARTIER ---------- */
    .pp-district { display: flex; flex-direction: column; gap: 8px; }
    .pp-district-search { position: relative; }
    .pp-district-search input {
        width: 100%;
        padding: 13px 40px 13px 16px;
        border: 1px solid rgba(85,16,29,.18);
        border-radius: 12px;
        background: #fffdf7;
        font-size: .92rem;
        color: var(--ink,#2a1a14);
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .pp-district-search input:focus {
        border-color: var(--gold,#e9b23f);
        box-shadow: 0 0 0 4px rgba(233,178,63,.18);
    }
    .pp-district-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9c8b7d;
        pointer-events: none;
    }
    .pp-district-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid rgba(85,16,29,.1);
        border-radius: 12px;
        background: #fffdf7;
        padding: 5px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        box-shadow: 0 8px 20px -12px rgba(61,11,21,.25);
    }
    .pp-district-list:empty { display: none; }
    .pp-district-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        text-align: left;
        font-size: .9rem;
        color: var(--ink,#2a1a14);
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }
    .pp-district-item:hover,
    .pp-district-item.is-active {
        background: rgba(233,178,63,.2);
        color: var(--maroon-800,#55101d);
    }
    .pp-district-empty {
        padding: 14px;
        text-align: center;
        font-size: .86rem;
        color: #9c8b7d;
        font-style: italic;
    }
    .pp-district-selected {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8rem;
        color: #395e26;
        background: rgba(92,138,55,.14);
        padding: 5px 12px;
        border-radius: 999px;
        align-self: flex-start;
    }
    .pp-district-selected button {
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        padding: 0 2px;
    }

    /* ============================================
       SECTIONS SOUS LE PRODUIT
       ============================================ */
    .pp-section { margin-top: 64px; }
    .pp-section-title {
        font-family: var(--serif,Georgia,serif);
        font-weight: 600;
        font-size: 1.45rem;
        color: var(--ink,#2a1a14);
        margin: 0 0 22px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gold,#e9b23f);
        display: inline-block;
        position: relative;
    }
    .pp-section-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 40%;
        height: 2px;
        background: var(--maroon-800,#55101d);
    }

    .pp-desc {
        font-size: .98rem;
        line-height: 1.85;
        color: #4a3a30;
        white-space: pre-line;
        max-width: 760px;
    }

    .pp-details {
        width: 100%;
        max-width: 780px;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 30px -18px rgba(61,11,21,.3);
        border: 1px solid rgba(85,16,29,.08);
    }
    .pp-details th,
    .pp-details td {
        padding: 14px 18px;
        text-align: left;
        font-size: .93rem;
        border-bottom: 1px solid rgba(85,16,29,.07);
    }
    .pp-details tr:last-child th,
    .pp-details tr:last-child td { border-bottom: 0; }
    .pp-details th {
        width: 38%;
        font-weight: 700;
        color: #6b5a4d;
        background: rgba(233,178,63,.09);
    }
    .pp-details td { color: var(--ink,#2a1a14); background: #fffdf7; }
    .pp-details td .fi { margin-right: 6px; }
    .pp-details td a {
        color: var(--maroon-800,#55101d);
        text-decoration: none;
        font-weight: 600;
    }
    .pp-details td a:hover { text-decoration: underline; }

    /* ============================================
       VISIONNEUSE
       ============================================ */
    .zoom-lightbox {
        position: fixed;
        inset: 0;
        z-index: 130;
        background: rgba(12,2,5,.94);
        display: none;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        backdrop-filter: blur(6px);
    }
    .zoom-lightbox.open { display: flex; }
    .zoom-lightbox img {
        max-width: 94%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        cursor: zoom-in;
        transition: transform .22s cubic-bezier(.2,.7,.3,1);
        user-select: none;
        -webkit-user-drag: none;
        box-shadow: 0 40px 80px -30px rgba(0,0,0,.8);
    }
    .zoom-lightbox img.is-zoomed { transform: scale(2.4); cursor: zoom-out; }
    .zoom-close {
        position: absolute;
        top: calc(14px + env(safe-area-inset-top));
        right: 14px;
        z-index: 3;
        width: 44px;
        height: 44px;
        border: 0;
        border-radius: 50%;
        background: rgba(255,253,247,.96);
        color: var(--maroon-900,#3d0b15);
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease;
    }
    .zoom-close:hover { background: var(--gold,#e9b23f); transform: rotate(90deg); }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 900px) {
        .pp-main { grid-template-columns: 1fr; gap: 30px; }
        .pp-gallery { position: static; }
        .pp-media { aspect-ratio: 1/1; }
        .pp-price { font-size: 1.85rem; }
        .pp-price-card { padding: 18px; }
        .pp-section { margin-top: 48px; }
        .pp-details th { width: 42%; }
    }
    @media (max-width: 480px) {
        .pp-media { aspect-ratio: 4/5; }
        .pp-price-card { flex-direction: column; align-items: flex-start; }
        .pp-delivery { flex-wrap: wrap; }
        .pp-delivery strong { margin-left: 0; text-align: left; }
        .pp-details th, .pp-details td { padding: 12px 14px; font-size: .88rem; }
    }
</style>

<section>
    <div class="wrap pp-wrap">

        {{-- ============ FIL D'ARIANE ============ --}}
        <nav class="pp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('home') }}">{{ __('home.nav_home') }}</a>
            <span class="sep">/</span>
            <a href="{{ route('books.index') }}">{{ __('home.nav_books') }}</a>
            @if($book->categorie)
                <span class="sep">/</span>
                <a href="{{ route('books.index', ['categorie' => $book->categorie]) }}">{{ $book->categorie }}</a>
            @endif
            <span class="sep">/</span>
            <span class="current">{{ $book->titre }}</span>
        </nav>

        <div class="pp-main">

            {{-- ============ GALERIE ============ --}}
            <div class="pp-gallery">
                <div class="pp-media-frame">
                    <div class="pp-media" id="ppMedia">
                        <img id="ppImage" src="{{ $image }}" alt="{{ $book->titre }}">
                        <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">{{ __('home.book_condition_' . $book->etat) }}</span>
                        <a href="{{ route('sellers.show', $book->seller) }}" class="pp-seller-float">
                            <span class="dot"></span>
                            {{ $sellerName }}
                        </a>
                        <span class="pp-zoom-btn" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m20 20-3.5-3.5M11 8v6M8 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- ============ RÉSUMÉ / ACHAT ============ --}}
            <div class="pp-summary">
                <div class="pp-meta-top">
                    @if($book->categorie)
                        <span class="book-genre" style="margin:0;">{{ $book->categorie }}</span>
                    @endif
                    <a href="{{ route('sellers.show', $book->seller) }}" class="pp-seller">{{ $sellerName }}</a>
                </div>

                <h1 class="pp-title">{{ $book->titre }}</h1>
                @if($book->auteur)
                    <p class="pp-author">{{ $book->auteur }}</p>
                @endif

                <div class="pp-price-card">
                    @if($book->prix_achat_client)
                        <strong class="pp-price">{{ number_format($book->prix_achat_client, 0, ',', ' ') }} Ar</strong>
                    @endif
                    @if($inStock)
                        <span class="pp-stock">{{ __('home.order_available_label') }} : <b>{{ $book->quantite }}</b></span>
                    @else
                        <span class="pp-stock is-out">{{ __('home.books_out_of_stock') }}</span>
                    @endif
                </div>

                <div class="pp-badges">
                    <span class="book-genre" style="background:rgba(233,178,63,.18); color:#8a5f14;">{{ __('home.book_condition_' . $book->etat) }}</span>
                    <span class="book-genre" style="background:rgba(92,138,55,.14); color:#395e26;">{{ $book->delai_livraison_label }}</span>
                    @if($book->langue_label)
                        <span class="book-genre" style="background:rgba(85,16,29,.08); color:var(--maroon-800);">
                            @if($book->langue_flag)<span class="fi fi-{{ $book->langue_flag }} fis"></span>@endif
                            {{ $book->langue_label }}
                        </span>
                    @endif
                    @if($book->format)
                        <span class="book-genre" style="background:rgba(92,138,55,.1); color:var(--green-700);">{{ $book->format_label }}</span>
                    @endif
                    @if($book->nombre_pages)
                        <span class="book-genre" style="background:rgba(85,16,29,.06); color:#6b5a4d;">{{ $book->pages_label }}</span>
                    @endif
                </div>

                @if($book->description)
                    <p class="pp-short-desc">{{ $book->description }}</p>
                @endif

                @if($isSeller)
                    <div class="pp-seller-block">
                        <p style="margin:0;">{{ __('home.order_seller_cant_order') }}</p>
                    </div>
                @elseif(! $inStock)
                    <div class="pp-seller-block">
                        <p style="margin:0;">{{ __('home.books_out_of_stock') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('cart.add') }}" id="ppForm" class="pp-buy">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="district" id="ppDistrictInput" value="{{ old('district') }}">

                        <div class="pp-district">
                            <span class="pp-label">{{ __('home.order_district_label') }}</span>
                            <div class="pp-district-search">
                                <input type="text" id="ppDistrictSearch"
                                       placeholder="{{ __('home.order_district_search_placeholder') }}"
                                       autocomplete="off" inputmode="search">
                                <span class="pp-district-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="pp-district-list" id="ppDistrictList" role="listbox"></div>
                            <span class="pp-district-selected" id="ppDistrictSelected" hidden>
                                <span id="ppDistrictSelectedLabel"></span>
                                <button type="button" id="ppDistrictClear" aria-label="&times;">&times;</button>
                            </span>
                        </div>

                        <div class="pp-delivery">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="1" y="9" width="14" height="9" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M15 12h3.5L21 15v3h-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="6" cy="19.5" r="1.8" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="17.5" cy="19.5" r="1.8" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            <span>{{ __('home.order_delivery_estimate_label') }}</span>
                            <strong>{{ $book->delai_livraison_label }}</strong>
                        </div>

                        <div class="pp-row">
                            <span class="pp-label">{{ __('home.order_quantity_label') }}</span>
                            <div class="pp-qty">
                                <div class="order-qty-stepper">
                                    <button type="button" id="ppQtyMinus" aria-label="-">&minus;</button>
                                    <input type="number" name="quantite" id="ppQty" value="1" min="1" max="{{ $book->quantite }}" step="1" inputmode="numeric">
                                    <button type="button" id="ppQtyPlus" aria-label="+">&plus;</button>
                                </div>
                            </div>
                        </div>

                        <div class="pp-row">
                            <span class="pp-label">{{ __('home.order_total_label') }}</span>
                            <strong class="pp-total" id="ppTotal">—</strong>
                        </div>

                        <button type="submit" class="pp-add">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="8" cy="21" r="1" stroke="currentColor" stroke-width="1.8"/>
                                <circle cx="19" cy="21" r="1" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('home.cart_add_button') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- ============ DESCRIPTION ============ --}}
        @if($book->description)
            <div class="pp-section">
                <h2 class="pp-section-title">{{ __('home.book_description_label') }}</h2>
                <div class="pp-desc">{{ $book->description }}</div>
            </div>
        @endif

        {{-- ============ CARACTÉRISTIQUES ============ --}}
        <div class="pp-section">
            <table class="pp-details">
                <tbody>
                    @if($book->auteur)
                        <tr><th>{{ __('home.book_author_label') }}</th><td>{{ $book->auteur }}</td></tr>
                    @endif
                    @if($book->categorie)
                        <tr><th>{{ __('home.book_category') }}</th><td>{{ $book->categorie }}</td></tr>
                    @endif
                    <tr><th>{{ __('home.book_condition') }}</th><td>{{ __('home.book_condition_' . $book->etat) }}</td></tr>
                    @if($book->langue_label)
                        <tr>
                            <th>{{ __('home.book_language_label') }}</th>
                            <td>@if($book->langue_flag)<span class="fi fi-{{ $book->langue_flag }} fis"></span>@endif{{ $book->langue_label }}</td>
                        </tr>
                    @endif
                    @if($book->format)
                        <tr><th>{{ __('home.book_format_label') }}</th><td>{{ $book->format_label }}</td></tr>
                    @endif
                    @if($book->nombre_pages)
                        <tr><th>{{ __('home.book_pages_label') }}</th><td>{{ $book->pages_label }}</td></tr>
                    @endif
                    <tr><th>{{ __('home.order_delivery_estimate_label') }}</th><td>{{ $book->delai_livraison_label }}</td></tr>
                    <tr>
                        <th>{{ __('home.nav_seller') }}</th>
                        <td><a href="{{ route('sellers.show', $book->seller) }}">{{ $sellerName }}</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ============ AUTRES LIVRES ============ --}}
        @if($related->isNotEmpty())
            <div class="pp-section">
                <h2 class="pp-section-title">{{ __('home.books_heading') }}</h2>
                <div class="book-grid">
                    @foreach($related as $item)
                        @include('partials.book-card', ['book' => $item])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

{{-- ============ VISIONNEUSE ZOOM ============ --}}
<div class="zoom-lightbox" id="zoomLightbox" role="dialog" aria-modal="true" aria-hidden="true">
    <img id="zoomImg" src="" alt="">
    <button type="button" class="zoom-close" id="zoomClose" aria-label="&times;">&times;</button>
</div>

<script>
(function(){
    var PRICE = {{ (int) ($book->prix_achat_client ?? 0) }};
    var MAX   = {{ (int) $book->quantite }};

    /* ---------- zoom au survol + visionneuse ---------- */
    var media = document.getElementById('ppMedia');
    var img   = document.getElementById('ppImage');
    var lb    = document.getElementById('zoomLightbox');
    var zimg  = document.getElementById('zoomImg');

    var canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    function setOrigin(e, el, target){
        var r = el.getBoundingClientRect();
        target.style.transformOrigin =
            ((e.clientX - r.left) / r.width * 100) + '% ' +
            ((e.clientY - r.top) / r.height * 100) + '%';
    }
    if (canHover) {
        media.addEventListener('mouseenter', function(e){ setOrigin(e, media, img); media.classList.add('is-zooming'); });
        media.addEventListener('mousemove',  function(e){ setOrigin(e, media, img); });
        media.addEventListener('mouseleave', function(){ media.classList.remove('is-zooming'); });
    }

    function openLb(){
        zimg.src = img.currentSrc || img.src;
        zimg.alt = img.alt;
        zimg.classList.remove('is-zoomed');
        zimg.style.transformOrigin = '50% 50%';
        media.classList.remove('is-zooming');
        lb.classList.add('open');
        lb.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeLb(){
        lb.classList.remove('open');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    media.addEventListener('click', openLb);
    document.getElementById('zoomClose').addEventListener('click', closeLb);
    lb.addEventListener('click', function(e){ if (e.target === lb) closeLb(); });
    zimg.addEventListener('click', function(e){
        setOrigin(e, zimg, zimg);
        zimg.classList.toggle('is-zoomed');
    });
    zimg.addEventListener('mousemove', function(e){
        if (zimg.classList.contains('is-zoomed')) setOrigin(e, zimg, zimg);
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && lb.classList.contains('open')) closeLb();
    });

    /* ---------- quantité + total ---------- */
    var qty   = document.getElementById('ppQty');
    var total = document.getElementById('ppTotal');
    if (qty && total) {
        function clamp(){
            var v = parseInt(qty.value, 10);
            if (isNaN(v) || v < 1) v = 1;
            if (v > MAX) v = MAX;
            qty.value = v;
            return v;
        }
        function updateTotal(){
            var v = clamp();
            total.textContent = PRICE ? (Math.round(PRICE * v).toLocaleString('fr-FR') + ' Ar') : '—';
        }
        document.getElementById('ppQtyMinus').addEventListener('click', function(){ qty.value = (parseInt(qty.value, 10) || 1) - 1; updateTotal(); });
        document.getElementById('ppQtyPlus').addEventListener('click',  function(){ qty.value = (parseInt(qty.value, 10) || 1) + 1; updateTotal(); });
        qty.addEventListener('input', updateTotal);
        updateTotal();
    }

    /* ---------- recherche quartier ---------- */
    var DISTRICTS = @json($districts);
    var dInput = document.getElementById('ppDistrictInput');
    var dSearch = document.getElementById('ppDistrictSearch');
    var dList = document.getElementById('ppDistrictList');
    var dSel = document.getElementById('ppDistrictSelected');
    var dSelLabel = document.getElementById('ppDistrictSelectedLabel');
    var dClear = document.getElementById('ppDistrictClear');
    var PLACEHOLDER = @json(__('home.order_district_search_placeholder'));
    var NO_RESULT = @json(__('home.order_district_no_result'));

    if (dSearch && dList) {
        var active = -1;
        function norm(s){ return (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }

        function render(q){
            var n = norm(q);
            var found = DISTRICTS.filter(function(d){ return norm(d).indexOf(n) !== -1; });
            dList.innerHTML = '';
            active = -1;
            if (!found.length) {
                var empty = document.createElement('div');
                empty.className = 'pp-district-empty';
                empty.textContent = NO_RESULT;
                dList.appendChild(empty);
                return;
            }
            found.slice(0, 100).forEach(function(name){
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'pp-district-item';
                b.setAttribute('role', 'option');
                b.dataset.value = name;
                b.textContent = name;
                b.addEventListener('click', function(){ choose(name); });
                dList.appendChild(b);
            });
        }
        function choose(name){
            dInput.value = name;
            dSelLabel.textContent = name;
            dSel.hidden = false;
            dSearch.value = '';
            dSearch.placeholder = name;
            dList.innerHTML = '';
        }
        function clearChoice(){
            dInput.value = '';
            dSel.hidden = true;
            dSelLabel.textContent = '';
            dSearch.value = '';
            dSearch.placeholder = PLACEHOLDER;
            render('');
            dSearch.focus();
        }
        function highlight(items){
            items.forEach(function(el, i){
                el.classList.toggle('is-active', i === active);
                if (i === active) el.scrollIntoView({ block: 'nearest' });
            });
        }

        dSearch.addEventListener('input', function(){ render(this.value); });
        dSearch.addEventListener('focus', function(){ if (!dList.children.length) render(this.value); });
        dSearch.addEventListener('keydown', function(e){
            var items = dList.querySelectorAll('.pp-district-item');
            if (!items.length) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, items.length - 1); highlight(items); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); highlight(items); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                if (active >= 0 && items[active]) choose(items[active].dataset.value);
                else if (items.length === 1) choose(items[0].dataset.value);
            } else if (e.key === 'Escape') { dList.innerHTML = ''; active = -1; }
        });
        dClear.addEventListener('click', clearChoice);
        document.addEventListener('click', function(e){
            if (!e.target.closest('.pp-district')) { dList.innerHTML = ''; active = -1; }
        });

        if (dInput.value) { choose(dInput.value); } else { render(''); }
    }
})();
</script>
@endsection