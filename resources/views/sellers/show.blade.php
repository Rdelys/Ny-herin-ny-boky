@extends('layouts.app')

@section('meta_title', ($seller->sellerProfile->nom_entreprise ?? $seller->name) . ' — ' . config('app.name'))

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
                        <article class="book-card">
                            <div class="book-cover">
                                <img src="{{ $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667' }}" alt="{{ $book->titre }}" loading="lazy">
                                <div class="book-cover-gradient"></div>
                                <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">{{ __('home.book_condition_' . $book->etat) }}</span>
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
                                    <span class="book-loc">{{ $book->prix_achat ? number_format($book->prix_achat, 0, ',', ' ').' Ar' : '—' }}</span>
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