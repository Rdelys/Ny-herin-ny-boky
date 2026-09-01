@extends('layouts.app')

@section('meta_title', __('home.meta_title'))
@section('meta_description', __('home.meta_description'))

@section('content')

    {{-- ============ HERO ============ --}}
    {{-- Fond : public/hero.png (voir .hero dans layouts/app.blade.php).
         L'illustration SVG du livre a été retirée. --}}
    <section class="hero">
        <div class="wrap">
            <div class="hero-inner">
                <p class="hero-eyebrow">{{ __('home.hero_eyebrow') }}</p>
                <h1>{{ __('home.hero_title') }}</h1>
                <p>{{ __('home.hero_subtitle') }}</p>
                <div class="hero-actions">
                    <a href="#livres" class="btn btn-primary">{{ __('home.hero_cta_browse') }}</a>
                    <a href="{{ url('/vendeur') }}" class="btn btn-ghost">{{ __('home.hero_cta_sell') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ LIVRES DISPONIBLES ============ --}}
    <section id="livres">
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.books_heading') }}</h2>
                    <p>{{ __('home.books_subheading') }}</p>
                </div>
                <a href="#" class="see-all">{{ __('home.books_see_all') }}</a>
            </div>

            @php
                // NOTE TEST : les photos utilisent des URLs de démo (aléatoire, seedées pour
                // rester stables au reload). Remplacez 'image' par vos vraies URLs / uploads
                // (ex: asset('storage/livres/xxx.jpg')) avant la mise en production.
                $books = $books ?? collect([
                    ['title' => 'Ny Ombalahibemaso', 'author' => 'Conte traditionnel', 'price' => '15 000 Ar', 'city' => 'Antananarivo', 'state' => 'neuf', 'genre' => 'Conte', 'image' => 'https://media.istockphoto.com/id/2263560827/fr/photo/livres-dans-la-biblioth%C3%A8que.webp?a=1&b=1&s=612x612&w=0&k=20&c=-Ma1T5ApOQYiWSZ-2cefogQDE1kbzIbXjdOPA11NcQs='],
                    ['title' => 'Ny Fitiavana Very', 'author' => 'Jean-Joseph Rabearivelo', 'price' => '9 500 Ar', 'city' => 'Fianarantsoa', 'state' => 'occasion', 'genre' => 'Poésie', 'image' => 'https://media.istockphoto.com/id/2263560827/fr/photo/livres-dans-la-biblioth%C3%A8que.webp?a=1&b=1&s=612x612&w=0&k=20&c=-Ma1T5ApOQYiWSZ-2cefogQDE1kbzIbXjdOPA11NcQs='],
                    ['title' => 'Dinitra sy Aretina', 'author' => 'Rado', 'price' => '12 000 Ar', 'city' => 'Toamasina', 'state' => 'neuf', 'genre' => 'Poésie', 'image' => 'https://media.istockphoto.com/id/2263560827/fr/photo/livres-dans-la-biblioth%C3%A8que.webp?a=1&b=1&s=612x612&w=0&k=20&c=-Ma1T5ApOQYiWSZ-2cefogQDE1kbzIbXjdOPA11NcQs='],
                    ['title' => 'Iarivointsara', 'author' => 'Elie Rajaonarison', 'price' => '8 000 Ar', 'city' => 'Mahajanga', 'state' => 'occasion', 'genre' => 'Roman', 'image' => 'https://media.istockphoto.com/id/2263560827/fr/photo/livres-dans-la-biblioth%C3%A8que.webp?a=1&b=1&s=612x612&w=0&k=20&c=-Ma1T5ApOQYiWSZ-2cefogQDE1kbzIbXjdOPA11NcQs='],
                ]);
            @endphp

            <div class="book-grid">
                @foreach($books as $book)
                    <article class="book-card">
                        <div class="book-cover">
                            <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" loading="lazy">
                            <div class="book-cover-gradient"></div>

                            <span class="book-tag {{ $book['state'] === 'occasion' ? 'occasion' : '' }}">
                                {{ $book['state'] === 'occasion' ? __('home.books_tag_used') : __('home.books_tag_new') }}
                            </span>

                            <button type="button" class="book-wishlist" aria-label="{{ __('home.books_wishlist') }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12 21s-7.5-4.6-10-9.1C.6 8.4 2 4.9 5.4 4.1c2-.5 4 .3 5 2 1-1.7 3-2.5 5-2 3.4.8 4.8 4.3 3.4 7.8C19.5 16.4 12 21 12 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <span class="book-price-float">{{ $book['price'] }}</span>

                            <button type="button" class="book-quickview">{{ __('home.books_quick_view') }}</button>
                        </div>
                        <div class="book-body">
                            <span class="book-genre">{{ $book['genre'] }}</span>
                            <h3 class="book-title">{{ $book['title'] }}</h3>
                            <p class="book-author">{{ $book['author'] }}</p>
                            <div class="book-foot">
                                <span class="book-loc">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 22s7-7.58 7-13A7 7 0 1 0 5 9c0 5.42 7 13 7 13z" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    {{ $book['city'] }}
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
        </div>
    </section>

    {{-- ============ VENDEURS DISPONIBLES ============ --}}
    <section class="sellers">
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.sellers_heading') }}</h2>
                    <p>{{ __('home.sellers_subheading') }}</p>
                </div>
                <a href="#" class="see-all">{{ __('home.sellers_see_all') }}</a>
            </div>

            @php
                $sellers = $sellers ?? collect([
                    ['name' => 'Miora R.', 'city' => 'Antananarivo', 'count' => 24, 'note' => '4.9'],
                    ['name' => 'Fenosoa A.', 'city' => 'Fianarantsoa', 'count' => 11, 'note' => '4.8'],
                    ['name' => 'Tolotra H.', 'city' => 'Toamasina', 'count' => 37, 'note' => '5.0'],
                ]);
            @endphp

            <div class="seller-grid">
                @foreach($sellers as $seller)
                    <article class="seller-card">
                        <div class="seller-avatar">{{ strtoupper(substr($seller['name'], 0, 1)) }}</div>
                        <div>
                            <h3 class="seller-name">{{ $seller['name'] }}</h3>
                            <p class="seller-meta">{{ $seller['city'] }}</p>
                            <div class="seller-stats">
                                <span>{{ $seller['count'] }} {{ __('home.sellers_books_count') }}</span>
                                <span>★ {{ $seller['note'] }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
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
                <a href="{{ url('/vendeur') }}" class="btn btn-primary">{{ __('home.cta_button') }}</a>
            </div>
        </div>
    </section>

@endsection