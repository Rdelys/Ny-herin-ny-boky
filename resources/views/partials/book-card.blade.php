@php
    $url        = route('books.show', $book);
    $image      = $book->image_path ? asset('storage/'.$book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$book->id.'/500/667';
    $sellerName = $book->seller->sellerProfile->nom_entreprise ?? $book->seller->name;
@endphp

<article class="book-card {{ $book->quantite <= 0 ? 'is-out-of-stock' : '' }}">

    {{-- ===== COUVERTURE ===== --}}
    <div class="book-cover">
        <a href="{{ $url }}" aria-label="{{ $book->titre }}" style="display:block; width:100%; height:100%;">
            <img src="{{ $image }}" alt="{{ $book->titre }}" loading="lazy">
        </a>

        <span class="book-tag {{ $book->etat !== 'neuf' ? 'occasion' : '' }}">
            {{ __('home.book_condition_' . $book->etat) }}
        </span>

        <button type="button" class="book-wishlist" aria-label="{{ __('home.books_wishlist') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21s-7.5-4.6-10-9.1C.6 8.4 2 4.9 5.4 4.1c2-.5 4 .3 5 2 1-1.7 3-2.5 5-2 3.4.8 4.8 4.3 3.4 7.8C19.5 16.4 12 21 12 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            </svg>
        </button>

        @if($book->quantite <= 0)
            <span class="book-out-of-stock">{{ __('home.books_out_of_stock') }}</span>
        @endif

        @if($book->prix_achat_client)
            <span class="book-price-float">{{ number_format($book->prix_achat_client, 0, ',', ' ') }} Ar</span>
        @endif

        <a href="{{ $url }}" class="book-quickview">{{ __('home.books_quick_view') }}</a>
    </div>

    {{-- ===== CORPS ===== --}}
    <div class="book-body">
        @if($book->categorie)
            <span class="book-genre">{{ $book->categorie }}</span>
        @endif

        <h3 class="book-title"><a href="{{ $url }}" style="color:inherit; text-decoration:none;">{{ $book->titre }}</a></h3>

        @if($book->auteur)
            <p class="book-author">{{ $book->auteur }}</p>
        @endif

        <a href="{{ route('sellers.show', $book->seller) }}" class="book-seller">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 7l9-4 9 4-9 4-9-4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M3 7v7c0 2 4 4 9 4s9-2 9-4V7" stroke="currentColor" stroke-width="1.6"/>
            </svg>
            {{ $sellerName }}
        </a>

        <span class="book-delivery-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            {{ $book->delai_livraison_label }}
        </span>

        <div class="book-meta-row">
            @if($book->langue_flag)
                <span class="book-meta-chip" title="{{ $book->langue_label }}">
                    <span class="fi fi-{{ $book->langue_flag }} fis"></span>
                    <span>{{ $book->langue_label }}</span>
                </span>
            @endif
            @if($book->format)
                <span class="book-meta-chip">{{ $book->format_label }}</span>
            @endif
            @if($book->nombre_pages)
                <span class="book-meta-chip">{{ $book->pages_label }}</span>
            @endif
        </div>

        <div class="book-foot">
            <span class="book-loc">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 22s7-7.58 7-13A7 7 0 1 0 5 9c0 5.42 7 13 7 13z" stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                {{ $book->seller->sellerProfile->localisation ?? '—' }}
            </span>

            @if($book->quantite > 0)
                <a href="{{ $url }}" class="book-add" aria-label="{{ $book->titre }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12H19M12 5V19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
</article>