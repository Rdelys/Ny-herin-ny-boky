@extends('layouts.app')

@section('meta_title', __('home.meta_title'))
@section('meta_description', __('home.meta_description'))

@section('content')

    {{-- ============ HERO ============ --}}
    {{-- Fond : public/hero.jpg (voir .hero dans layouts/app.blade.php).
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
                // NOTE TEST : les photos utilisent picsum.photos (aléatoire, seedé pour
                // rester stable au reload). Remplacez 'image' par vos vraies URLs / uploads
                // (ex: asset('storage/livres/xxx.jpg')) avant la mise en production.
                $books = $books ?? collect([
                    ['title' => 'Ny Ombalahibemaso', 'author' => 'Conte traditionnel', 'price' => '15 000 Ar', 'city' => 'Antananarivo', 'state' => 'neuf', 'genre' => 'Conte', 'image' => 'https://plus.unsplash.com/premium_photo-1677187301660-5e557d9c0724?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bGl2cmV8ZW58MHx8MHx8fDA%3D'],
                    ['title' => 'Ny Fitiavana Very', 'author' => 'Jean-Joseph Rabearivelo', 'price' => '9 500 Ar', 'city' => 'Fianarantsoa', 'state' => 'occasion', 'genre' => 'Poésie', 'image' => 'https://plus.unsplash.com/premium_photo-1677187301660-5e557d9c0724?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bGl2cmV8ZW58MHx8MHx8fDA%3D'],
                    ['title' => 'Dinitra sy Aretina', 'author' => 'Rado', 'price' => '12 000 Ar', 'city' => 'Toamasina', 'state' => 'neuf', 'genre' => 'Poésie', 'image' => 'https://plus.unsplash.com/premium_photo-1677187301660-5e557d9c0724?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bGl2cmV8ZW58MHx8MHx8fDA%3D'],
                    ['title' => 'Iarivointsara', 'author' => 'Elie Rajaonarison', 'price' => '8 000 Ar', 'city' => 'Mahajanga', 'state' => 'occasion', 'genre' => 'Roman', 'image' => 'https://plus.unsplash.com/premium_photo-1677187301660-5e557d9c0724?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bGl2cmV8ZW58MHx8MHx8fDA%3D'],
                ]);
            @endphp

            <div class="book-grid">
                @foreach($books as $book)
                    <article class="book-card">
                        <div class="book-cover">
                            <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" loading="lazy">
                            <span class="book-tag {{ $book['state'] === 'occasion' ? 'occasion' : '' }}">
                                {{ $book['state'] === 'occasion' ? __('home.books_tag_used') : __('home.books_tag_new') }}
                            </span>
                        </div>
                        <div class="book-body">
                            <span class="book-genre">{{ $book['genre'] }}</span>
                            <h3 class="book-title">{{ $book['title'] }}</h3>
                            <p class="book-author">{{ $book['author'] }}</p>
                            <div class="book-foot">
                                <span class="book-price">{{ $book['price'] }}</span>
                                <span class="book-loc">{{ $book['city'] }}</span>
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