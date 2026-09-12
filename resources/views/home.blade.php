@extends('layouts.app')

@section('meta_title', __('home.meta_title'))
@section('meta_description', __('home.meta_description'))

@section('content')

    {{-- ============ HERO ============ --}}
    <section class="hero">
        <div class="wrap">
            <div class="hero-inner">
                <p class="hero-eyebrow">{{ __('home.hero_eyebrow') }}</p>
                <h1>{{ __('home.hero_title') }}</h1>
                <p>{{ __('home.hero_subtitle') }}</p>
                <div class="hero-actions">
                    <a href="#livres" class="btn btn-primary">{{ __('home.hero_cta_browse') }}</a>
                    <button type="button" class="btn btn-ghost" data-auth-open="registerSeller">{{ __('home.hero_cta_sell') }}</button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ LIVRES DISPONIBLES (dynamique, depuis la BDD) ============ --}}
    <section id="livres">
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.books_heading') }}</h2>
                    <p>{{ __('home.books_subheading') }}</p>
                </div>
                <a href="{{ route('books.index') }}" class="see-all">{{ __('home.books_see_all') }}</a>
            </div>

            @if($books->isEmpty())
                <p style="color:#6b5a4d;">{{ __('home.books_none_yet') }}</p>
            @else
                <div class="book-grid">
                    @foreach($books as $book)
                        <article class="book-card">
                            <div class="book-cover">
                                <img src="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}" alt="{{ $book->titre }}" loading="lazy">
                                <div class="book-cover-gradient"></div>

                                <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">
                                    {{ __('home.book_condition_' . $book->etat) }}
                                </span>

                                <button type="button" class="book-wishlist" aria-label="{{ __('home.books_wishlist') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 21s-7.5-4.6-10-9.1C.6 8.4 2 4.9 5.4 4.1c2-.5 4 .3 5 2 1-1.7 3-2.5 5-2 3.4.8 4.8 4.3 3.4 7.8C19.5 16.4 12 21 12 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                @if($book->prix_achat_client)
                                    <span class="book-price-float">{{ number_format($book->prix_achat_client, 0, ',', ' ') }} Ar</span>
                                @endif

                                <button type="button" class="book-quickview">{{ __('home.books_quick_view') }}</button>
                            </div>
                            <div class="book-body">
                                @if($book->categorie)
                                    <span class="book-genre">{{ $book->categorie }}</span>
                                @endif
                                <h3 class="book-title">{{ $book->titre }}</h3>
                                @if($book->auteur)
                                    <p class="book-author">{{ $book->auteur }}</p>
                                @endif
                                <a href="{{ route('sellers.show', $book->seller) }}" class="book-seller">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M3 7l9-4 9 4-9 4-9-4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                        <path d="M3 7v7c0 2 4 4 9 4s9-2 9-4V7" stroke="currentColor" stroke-width="1.6"/>
                                    </svg>
                                    {{ $book->seller->sellerProfile->nom_entreprise ?? $book->seller->name }}
                                </a>
                                <div class="book-foot">
                                    <span class="book-loc">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M12 22s7-7.58 7-13A7 7 0 1 0 5 9c0 5.42 7 13 7 13z" stroke="currentColor" stroke-width="1.8"/>
                                            <circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        {{ $book->seller->sellerProfile->localisation ?? '—' }}
                                    </span>
                                    <button type="button" class="book-add" aria-label="{{ __('home.books_add') }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M5 12H19M12 5V19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============ VENDEURS DISPONIBLES (dynamique, depuis la BDD) ============ --}}
    <section class="sellers">
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.sellers_heading') }}</h2>
                    <p>{{ __('home.sellers_subheading') }}</p>
                </div>
                <a href="{{ route('sellers.index') }}" class="see-all">{{ __('home.sellers_see_all') }}</a>
            </div>

            @if($sellers->isEmpty())
                <p style="color:rgba(246,239,221,.65);">{{ __('home.sellers_none_yet') }}</p>
            @else
                <div class="seller-grid">
                    @foreach($sellers as $seller)
                        <a href="{{ route('sellers.show', $seller) }}" class="seller-card">
                            <div class="seller-avatar">{{ strtoupper(substr($seller->sellerProfile->nom_entreprise ?? $seller->name, 0, 1)) }}</div>
                            <div>
                                <h3 class="seller-name">{{ $seller->sellerProfile->nom_entreprise ?? $seller->name }}</h3>
                                <p class="seller-meta">{{ $seller->sellerProfile->localisation ?? '—' }}</p>
                                <div class="seller-stats">
                                    <span>{{ $seller->books_count }} {{ __('home.sellers_books_count') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============ CTA BAND ============ --}}
    <section>
        <div class="wrap">
            <div class="cta-band">
                <div>
                    <h3>{{ __('home.cta_title') }}</h3>
                    <p>{{ __('home.cta_subtitle') }}</p>
                </div>
                <button type="button" class="btn btn-primary" data-auth-open="registerSeller">{{ __('home.cta_button') }}</button>
            </div>
        </div>
    </section>

@endsection