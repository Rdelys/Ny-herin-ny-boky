@php
    $mode    = $mode ?? 'price';   // price | tag
    $size    = $size ?? 'md';      // sm | md
    $enPromo = $book->en_promo;
@endphp

@once
<style>
    .bp{
        display: inline-flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 2px 8px;
        min-width: 0;
        line-height: 1.15;
    }
    .bp-now{
        font-family: var(--serif, Georgia, serif);
        font-weight: 700;
        color: var(--maroon-800, #55101d);
    }
    .bp-sm .bp-now{ font-size: 1rem; }
    .bp-md .bp-now{ font-size: 1.25rem; }
    .bp.is-promo .bp-now{ color: #b3261e; }
    .bp-old{
        font-size: .76rem;
        font-weight: 500;
        color: #9c8b7d;
        text-decoration: line-through;
        text-decoration-thickness: 1.5px;
    }
    .bp-badge{
        align-self: center;
        font-size: .64rem;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(179,38,30,.12);
        color: #b3261e;
        white-space: nowrap;
    }
    .book-promo-tag{
        position: absolute;
        top: 42px;
        left: 10px;
        z-index: 2;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 5px 10px;
        border-radius: 999px;
        background: #b3261e;
        color: #fff;
        box-shadow: 0 4px 10px -4px rgba(0,0,0,.35);
    }
</style>
@endonce

@if($mode === 'tag')
    @if($enPromo)
        <span class="book-promo-tag">{{ $book->promo_label }}</span>
    @endif
@else
    @if($book->prix_achat_client)
        <span class="bp bp-{{ $size }} {{ $enPromo ? 'is-promo' : '' }}">
            @if($enPromo)
                <s class="bp-old">{{ number_format($book->prix_achat_client_original, 0, ',', ' ') }} Ar</s>
            @endif
            <strong class="bp-now">{{ number_format($book->prix_achat_client, 0, ',', ' ') }} Ar</strong>
            @if($enPromo)
                <span class="bp-badge">{{ $book->promo_label }}</span>
            @endif
        </span>
    @else
        <span class="bp bp-{{ $size }}"><strong class="bp-now">—</strong></span>
    @endif
@endif