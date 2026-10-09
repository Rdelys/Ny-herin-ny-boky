@extends('layouts.app')

@section('meta_title', __('home.nav_books') . ' — ' . config('app.name'))
@section('meta_description', __('home.meta_books_description'))

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
    @include('partials.book-card', ['book' => $book])
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