@extends('layouts.app')

@php
    $sellerName = $seller->sellerProfile->nom_entreprise ?? $seller->name;
    $sellerLocation = $seller->sellerProfile->localisation ?? null;
@endphp

@section('meta_title', $sellerName . ' — ' . config('app.name'))
@section('meta_description', __('home.meta_seller_description', [
    'name' => $sellerName,
    'location' => $sellerLocation ? ' (' . $sellerLocation . ')' : '',
    'count' => $books->total(),
]))

@push('head')
    {{-- Données structurées : le vendeur est une librairie, avec le
         catalogue visible sur cette page. --}}
    @php
        $storeSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => $sellerName,
            'url' => route('sellers.show', $seller),
            'address' => $sellerLocation
                ? ['@type' => 'PostalAddress', 'addressLocality' => $sellerLocation]
                : null,
            'makesOffer' => $books->take(10)->map(fn ($book) => [
                '@type' => 'Offer',
                'price' => $book->prix_achat_client,
                'priceCurrency' => 'MGA',
                'availability' => $book->quantite > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemOffered' => array_filter([
                    '@type' => 'Book',
                    'name' => $book->titre,
                    'author' => $book->auteur,
                ]),
            ])->values()->all(),
        ], fn ($value) => $value !== null && $value !== []);
    @endphp
    <script type="application/ld+json">
    {!! json_encode($storeSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div class="seller-avatar" style="width:56px; height:56px; font-size:1.3rem;">
                        {{ strtoupper(substr($seller->sellerProfile->nom_entreprise ?? $seller->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 style="margin-bottom:2px;">{{ $seller->sellerProfile->nom_entreprise ?? $seller->name }}</h2>
                        <p>{{ $seller->sellerProfile->localisation ?? '—' }} · {{ $books->total() }} {{ __('home.sellers_books_count') }}</p>
                    </div>
                </div>
                <a href="{{ route('sellers.index') }}" class="see-all">&larr; {{ __('home.sellers_page_title') }}</a>
            </div>

            @if($books->isEmpty())
                <p style="color:#7a6a5d;">{{ __('home.book_none_yet') }}</p>
            @else
                <div class="book-grid">
                    @foreach($books as $book)
                        <article class="book-card {{ $book->quantite <= 0 ? 'is-out-of-stock' : '' }}">
                            <div class="book-cover">
                                <img src="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}" alt="{{ $book->titre }}" loading="lazy">
                                <div class="book-cover-gradient"></div>
                                <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">{{ __('home.book_condition_' . $book->etat) }}</span>
                                @if($book->quantite <= 0)
                                    <span class="book-out-of-stock">{{ __('home.books_out_of_stock') }}</span>
                                @endif
                            </div>
                            <div class="book-body">
                                @if($book->categorie)
                                    <span class="book-genre">{{ $book->categorie }}</span>
                                @endif
                                <h3 class="book-title">{{ $book->titre }}</h3>
                                @if($book->auteur)
                                    <p class="book-author">{{ $book->auteur }}</p>
                                @endif
                                <div class="book-foot">
                                    <span class="book-loc">{{ $book->prix_achat_client ? number_format($book->prix_achat_client, 0, ',', ' ').' Ar' : '—' }}</span>
                                    @if($book->quantite > 0)
                                    <button type="button" class="book-add" aria-label="{{ __('home.books_add') }}"
                                        data-book-order
                                    data-book-id="{{ $book->id }}"
                                    data-book-author="{{ $book->auteur }}"
                                    data-book-category="{{ $book->categorie }}"
                                    data-book-condition="{{ __('home.book_condition_' . $book->etat) }}"
                                    data-book-description="{{ $book->description }}"
                                        data-book-title="{{ $book->titre }}"
                                        data-book-image="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}"
                                        data-book-seller="{{ $seller->sellerProfile->nom_entreprise ?? $seller->name }}"
                                        data-book-price="{{ $book->prix_achat_client }}"
                                        data-book-max="{{ $book->quantite }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M5 12H19M12 5V19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
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
@endsection