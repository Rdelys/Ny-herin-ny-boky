@extends('layouts.app')

@section('meta_title', __('home.nav_books') . ' — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.nav_books') }}</h2>
                    <p>
                        @if($query !== '')
                            {{ __('home.catalog_results_for') }} « {{ $query }} »
                        @elseif($categorie !== '')
                            {{ $categorie }}
                        @else
                            {{ __('home.catalog_all_books') }}
                        @endif
                        — {{ $books->total() }} {{ __('home.book_count_suffix') }}
                    </p>
                </div>
            </div>

            {{-- Filtres : recherche + catégorie --}}
            <form method="GET" action="{{ route('books.index') }}" class="catalog-filters">
                <input type="search" name="q" value="{{ $query }}" placeholder="{{ __('home.search_placeholder') }}">
                <select name="categorie" onchange="this.form.submit()">
                    <option value="">{{ __('home.catalog_all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected($categorie === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">{{ __('home.catalog_search_button') }}</button>
                @if($query !== '' || $categorie !== '')
                    <a href="{{ route('books.index') }}" class="see-all">{{ __('home.catalog_reset') }}</a>
                @endif
            </form>

            @if($books->isEmpty())
                <p style="color:#6b5a4d;">{{ __('home.catalog_no_results') }}</p>
            @else
                <div class="book-grid">
                    @foreach($books as $book)
                        <article class="book-card">
                            <div class="book-cover">
                                <img src="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}" alt="{{ $book->titre }}" loading="lazy">
                                <div class="book-cover-gradient"></div>
                                <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">{{ __('home.book_condition_' . $book->etat) }}</span>
                                @if($book->prix_achat_client)
                                    <span class="book-price-float">{{ number_format($book->prix_achat_client, 0, ',', ' ') }} Ar</span>
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
                                <a href="{{ route('sellers.show', $book->seller) }}" class="book-seller">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M3 7l9-4 9 4-9 4-9-4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                        <path d="M3 7v7c0 2 4 4 9 4s9-2 9-4V7" stroke="currentColor" stroke-width="1.6"/>
                                    </svg>
                                    {{ $book->seller->sellerProfile->nom_entreprise ?? $book->seller->name }}
                                </a>
                                <div class="book-foot">
                                    <span class="book-loc">{{ $book->seller->sellerProfile->localisation ?? '—' }}</span>
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