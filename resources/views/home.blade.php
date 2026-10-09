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

    {{-- ============ LIVRES DISPONIBLES ============ --}}
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
    @include('partials.book-card', ['book' => $book])
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

{{-- ============ STYLES DES CARDS (à déplacer dans votre CSS global si besoin) ============ --}}
<style>
/* ============================================================
   GRILLE
============================================================ */
.book-grid{
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 22px;
}

/* ============================================================
   CARD
============================================================ */
.book-card{
    position: relative;
    display: flex;
    flex-direction: column;
    background: #fffdf7;
    border: 1px solid rgba(85,16,29,.08);
    border-radius: 18px;
    overflow: hidden;
    cursor: pointer;
    transition: transform .22s cubic-bezier(.2,.8,.2,1),
                box-shadow .22s ease,
                border-color .22s ease;
    box-shadow: 0 2px 6px -2px rgba(61,11,21,.08);
}
.book-card:hover{
    transform: translateY(-4px);
    border-color: rgba(233,178,63,.55);
    box-shadow: 0 18px 32px -18px rgba(61,11,21,.35);
}
.book-card:focus-visible{
    outline: 2px solid var(--gold);
    outline-offset: 2px;
}
.book-card.is-out-of-stock{
    opacity: .78;
    cursor: not-allowed;
}
.book-card.is-out-of-stock:hover{
    transform: none;
    box-shadow: 0 2px 6px -2px rgba(61,11,21,.08);
}

/* ============================================================
   COUVERTURE
============================================================ */
.book-cover{
    position: relative;
    aspect-ratio: 4 / 5;
    overflow: hidden;
    background: linear-gradient(160deg, #f6efdd, #e8dcc0);
}
.book-cover img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .5s cubic-bezier(.2,.8,.2,1);
}
.book-card:hover .book-cover img{
    transform: scale(1.05);
}
.book-cover::after{
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg,
                rgba(0,0,0,0) 45%,
                rgba(20,4,7,.08) 65%,
                rgba(20,4,7,.55) 100%);
    pointer-events: none;
}

/* ============================================================
   BADGES SUR LA COUVERTURE
============================================================ */
.book-tag{
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .02em;
    text-transform: uppercase;
    padding: 5px 10px;
    border-radius: 999px;
    background: rgba(233,178,63,.95);
    color: #5a3a08;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    box-shadow: 0 4px 10px -4px rgba(0,0,0,.25);
}
.book-tag.occasion{
    background: rgba(85,16,29,.9);
    color: #f6efdd;
}

.book-wishlist{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    width: 32px;
    height: 32px;
    border: 0;
    border-radius: 50%;
    background: rgba(255,253,247,.92);
    color: var(--maroon-900, #3d0b15);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .15s ease, transform .15s ease, color .15s ease;
    box-shadow: 0 4px 10px -4px rgba(0,0,0,.3);
}
.book-wishlist:hover{
    background: var(--gold, #e9b23f);
    color: #3d0b15;
    transform: scale(1.08);
}

.book-out-of-stock{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-6deg);
    z-index: 3;
    background: rgba(61,11,21,.92);
    color: #f6efdd;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 8px 16px;
    border-radius: 6px;
    box-shadow: 0 8px 20px -6px rgba(0,0,0,.5);
}

.book-price-float{
    position: absolute;
    left: 12px;
    bottom: 12px;
    z-index: 2;
    font-family: var(--serif, Georgia, serif);
    font-weight: 700;
    font-size: 1.05rem;
    color: #fffdf7;
    background: rgba(61,11,21,.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 6px 12px;
    border-radius: 999px;
    letter-spacing: .01em;
    box-shadow: 0 4px 12px -4px rgba(0,0,0,.45);
}

.book-quickview{
    position: absolute;
    right: 12px;
    bottom: 12px;
    z-index: 2;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .02em;
    padding: 7px 13px;
    border: 0;
    border-radius: 999px;
    background: rgba(255,253,247,.95);
    color: var(--maroon-900, #3d0b15);
    cursor: pointer;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity .2s ease, transform .2s ease, background .15s ease;
    box-shadow: 0 6px 14px -6px rgba(0,0,0,.35);
}
.book-card:hover .book-quickview{
    opacity: 1;
    transform: translateY(0);
}
.book-quickview:hover{
    background: var(--gold, #e9b23f);
}

/* ============================================================
   CORPS DE CARD
============================================================ */
.book-body{
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 14px 14px 12px;
    flex: 1;
}

.book-genre{
    align-self: flex-start;
    font-size: .64rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6b5a4d;
    background: rgba(233,178,63,.18);
    padding: 4px 9px;
    border-radius: 999px;
    line-height: 1;
}

.book-title{
    font-family: var(--serif, Georgia, serif);
    font-weight: 700;
    font-size: 1.02rem;
    line-height: 1.25;
    color: var(--ink, #2a1a14);
    margin: 2px 0 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-author{
    font-size: .82rem;
    color: #8a7a6d;
    font-style: italic;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-seller{
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .76rem;
    font-weight: 600;
    color: #7a6a5d;
    text-decoration: none;
    transition: color .15s ease;
    align-self: flex-start;
    max-width: 100%;
}
.book-seller:hover{ color: var(--maroon-800, #55101d); }
.book-seller svg{ flex-shrink: 0; opacity: .7; }
.book-seller span{ white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ============================================================
   BADGE LIVRAISON
============================================================ */
.book-delivery-badge{
    display: inline-flex;
    align-items: center;
    gap: 6px;
    align-self: flex-start;
    font-size: .74rem;
    font-weight: 700;
    color: #395e26;
    background: rgba(92,138,55,.14);
    padding: 5px 10px;
    border-radius: 999px;
    line-height: 1;
}
.book-delivery-badge svg{ flex-shrink: 0; }

/* ============================================================
   MÉTA (langue, format, pages)
============================================================ */
.book-meta-row{
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 2px;
}
.book-meta-chip{
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .7rem;
    font-weight: 600;
    color: #6b5a4d;
    background: rgba(85,16,29,.06);
    padding: 4px 9px;
    border-radius: 999px;
    line-height: 1;
}
.book-meta-chip svg{ flex-shrink: 0; opacity: .75; }
.book-meta-chip .fi{ font-size: .85rem; }

/* ============================================================
   PIED DE CARD
============================================================ */
.book-foot{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px dashed rgba(85,16,29,.12);
}
.book-loc{
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .74rem;
    color: #8a7a6d;
    min-width: 0;
}
.book-loc svg{ flex-shrink: 0; opacity: .75; }
.book-loc span,
.book-loc{ white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.book-add{
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 50%;
    background: var(--maroon-900, #3d0b15);
    color: #f6efdd;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .15s ease, transform .15s ease, box-shadow .15s ease;
    box-shadow: 0 6px 14px -6px rgba(61,11,21,.5);
}
.book-add:hover{
    background: var(--maroon-800, #55101d);
    transform: scale(1.08) rotate(90deg);
    box-shadow: 0 10px 20px -8px rgba(61,11,21,.6);
}

/* ============================================================
   RESPONSIVE
============================================================ */
@media (max-width: 640px){
    .book-grid{
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 14px;
    }
    .book-body{ padding: 11px 11px 10px; gap: 6px; }
    .book-title{ font-size: .92rem; }
    .book-author{ font-size: .76rem; }
    .book-price-float{ font-size: .88rem; padding: 5px 10px; }
    .book-tag{ font-size: .6rem; padding: 4px 8px; }
    .book-wishlist{ width: 28px; height: 28px; }
    .book-add{ width: 30px; height: 30px; }
    .book-quickview{
        opacity: 1;
        transform: none;
        font-size: .66rem;
        padding: 6px 10px;
    }
}
</style>