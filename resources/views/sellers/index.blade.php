@extends('layouts.app')

@section('meta_title', 'Nos vendeurs — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>Nos vendeurs</h2>
                    <p>Tous les vendeurs inscrits sur Ny Herin'ny Boky.</p>
                </div>
            </div>

            @if($sellers->isEmpty())
                <p style="color:#7a6a5d;">Aucun vendeur pour l'instant. Soyez le premier !</p>
            @else
                <div class="seller-grid seller-grid-light">
                    @foreach($sellers as $seller)
                        <article class="seller-card">
                            <div class="seller-avatar">{{ strtoupper(substr($seller->sellerProfile->nom_entreprise ?? $seller->name, 0, 1)) }}</div>
                            <div>
                                <h3 class="seller-name">{{ $seller->sellerProfile->nom_entreprise ?? $seller->name }}</h3>
                                <p class="seller-meta">{{ $seller->sellerProfile->localisation ?? '—' }}</p>
                                <div class="seller-stats">
                                    <span>{{ $seller->books_count }} livre(s)</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($sellers->hasPages())
                    <div class="pager">
                        @if($sellers->onFirstPage())
                            <span class="pager-btn disabled">&larr; Précédent</span>
                        @else
                            <a href="{{ $sellers->previousPageUrl() }}" class="pager-btn">&larr; Précédent</a>
                        @endif
                        <span class="pager-info">Page {{ $sellers->currentPage() }} / {{ $sellers->lastPage() }}</span>
                        @if($sellers->hasMorePages())
                            <a href="{{ $sellers->nextPageUrl() }}" class="pager-btn">Suivant &rarr;</a>
                        @else
                            <span class="pager-btn disabled">Suivant &rarr;</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection