@extends('layouts.app')

@section('meta_title', __('home.sellers_page_title') . ' — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.sellers_page_title') }}</h2>
                    <p>{{ __('home.sellers_page_subtitle') }}</p>
                </div>
            </div>

            @if($sellers->isEmpty())
                <p style="color:#7a6a5d;">{{ __('home.sellers_page_none') }}</p>
            @else
                <div class="seller-grid seller-grid-light">
                    @foreach($sellers as $seller)
                        <article class="seller-card">
                            <div class="seller-avatar">{{ strtoupper(substr($seller->sellerProfile->nom_entreprise ?? $seller->name, 0, 1)) }}</div>
                            <div>
                                <h3 class="seller-name">{{ $seller->sellerProfile->nom_entreprise ?? $seller->name }}</h3>
                                <p class="seller-meta">{{ $seller->sellerProfile->localisation ?? '—' }}</p>
                                <div class="seller-stats">
                                    <span>{{ $seller->books_count }} {{ __('home.sellers_books_count') }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($sellers->hasPages())
                    <div class="pager">
                        @if($sellers->onFirstPage())
                            <span class="pager-btn disabled">&larr; {{ __('home.pager_previous') }}</span>
                        @else
                            <a href="{{ $sellers->previousPageUrl() }}" class="pager-btn">&larr; {{ __('home.pager_previous') }}</a>
                        @endif
                        <span class="pager-info">{{ __('home.pager_page_of') }} {{ $sellers->currentPage() }} / {{ $sellers->lastPage() }}</span>
                        @if($sellers->hasMorePages())
                            <a href="{{ $sellers->nextPageUrl() }}" class="pager-btn">{{ __('home.pager_next') }} &rarr;</a>
                        @else
                            <span class="pager-btn disabled">{{ __('home.pager_next') }} &rarr;</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection