@php
    $isSeller = auth()->check() && auth()->user()->isSeller();
@endphp

<style>
    /* ============ MODAL FICHE PRODUIT (aperçu rapide / ajout au panier) ============ */
    .pd-panel{
        position: relative;
        display: flex;
        width: 100%;
        max-width: 980px;
        max-height: 92vh;
        overflow: hidden;
        background: var(--cream);
        border-radius: 24px;
        box-shadow: 0 30px 60px -20px rgba(0,0,0,.5);
        transform: translateY(14px) scale(.98);
        transition: transform .22s cubic-bezier(.2,.8,.2,1);
    }
    .modal-overlay.open .pd-panel{ transform: none; }

    .pd-close{
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 5;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        background: rgba(255,253,247,.95);
        color: var(--maroon-900);
        font-size: 1.4rem;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 16px -6px rgba(0,0,0,.35);
        transition: background .15s ease, transform .15s ease;
    }
    .pd-close:hover{ background: var(--gold); transform: rotate(90deg); }

    .pd-grid{
        display: grid;
        grid-template-columns: minmax(0, 5fr) minmax(0, 6fr);
        width: 100%;
        max-height: 92vh;
    }

    /* ---- image à gauche ---- */
    .pd-media{
        position: relative;
        min-height: 460px;
        background: linear-gradient(160deg, var(--cream-dim), #d9c99f);
    }
    .pd-media img{
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pd-media::after{
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 70%, rgba(20,4,7,.35) 100%);
        pointer-events: none;
    }

    /* ---- infos à droite (c'est cette colonne qui défile) ---- */
    .pd-info{
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 40px 38px 32px;
        overflow-y: auto;
        max-height: 92vh;
        min-width: 0;
    }
    .pd-meta-top{ display: flex; align-items: center; flex-wrap: wrap; gap: 8px; padding-right: 44px; }
    .pd-seller{
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8rem;
        font-weight: 600;
        color: #8a7a6d;
    }
    .pd-seller::before{
        content: '';
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--gold);
        flex-shrink: 0;
    }
    .pd-title{
        font-family: var(--serif);
        font-weight: 600;
        font-size: clamp(1.45rem, 2.6vw, 2rem);
        line-height: 1.15;
        color: var(--ink);
        margin: 0;
    }
    .pd-author{ margin: -6px 0 0; font-size: .95rem; color: #7a6a5d; font-style: italic; }

    .pd-price-row{
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 0;
        border-top: 1px dashed rgba(85,16,29,.16);
        border-bottom: 1px dashed rgba(85,16,29,.16);
    }
    .pd-price{
        font-family: var(--serif);
        font-weight: 700;
        font-size: 1.9rem;
        color: var(--maroon-800);
        line-height: 1;
    }
    .pd-stock{
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--green-700);
        background: rgba(92,138,55,.12);
        padding: 4px 11px;
        border-radius: 999px;
    }
    .pd-stock::before{ content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--green-500); }

    .pd-badges{ display: flex; flex-wrap: wrap; gap: 6px; }
    .pd-badges .book-genre{ margin: 0; }

    .pd-desc-title{
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #9c8b7d;
        margin: 4px 0 -6px;
    }
    .pd-desc{
        margin: 0;
        font-size: .93rem;
        line-height: 1.7;
        color: #4a3a30;
        max-height: 150px;
        overflow-y: auto;
    }
    .pd-desc:empty{ display: none; }

    .pd-delivery{
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(92,138,55,.08);
        border-radius: 12px;
        padding: 11px 14px;
        font-size: .86rem;
        color: #395e26;
    }
    .pd-delivery svg{ flex-shrink: 0; }
    .pd-delivery span{ color: #6b5a4d; }
    .pd-delivery strong{ margin-left: auto; text-align: right; }

    .pd-buy{ display: flex; flex-direction: column; gap: 14px; margin-top: 4px; }
    .pd-buy-row{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .pd-buy-row .pd-label{ font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: #9c8b7d; }
    .pd-total{ font-family: var(--serif); font-weight: 700; font-size: 1.35rem; color: var(--maroon-800); }
    .pd-qty .order-qty-stepper button{ width: 40px; height: 40px; font-size: 1.2rem; }
    .pd-qty .order-qty-stepper input{ width: 52px; height: 40px; background: transparent; }

    .pd-add{
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 15px 22px;
        border: 0;
        border-radius: 999px;
        background: var(--maroon-900);
        color: var(--cream);
        font-weight: 700;
        font-size: 1rem;
        transition: background .15s ease, transform .15s ease, box-shadow .15s ease;
    }
    .pd-add:hover{ background: var(--maroon-800); transform: translateY(-1px); box-shadow: 0 12px 24px -12px rgba(85,16,29,.6); }
    .pd-add:disabled{ opacity: .6; cursor: wait; transform: none; }

    .pd-seller-block{
        margin: auto 0;
        text-align: center;
        background: rgba(233,178,63,.12);
        border: 1px dashed var(--gold);
        border-radius: 14px;
        padding: 20px;
        color: #6b5a4d;
        font-size: .92rem;
    }

    /* ---- tablette ---- */
    @media (max-width: 900px){
        .pd-grid{ grid-template-columns: minmax(0, 4fr) minmax(0, 6fr); }
        .pd-info{ padding: 34px 26px 26px; }
    }

    /* ---- mobile : bottom sheet, image en haut ---- */
    @media (max-width: 720px){
        .pd-panel{
            display: block;
            max-height: 94vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 22px 22px 0 0;
        }
        .pd-grid{ display: block; max-height: none; }
        .pd-media{ min-height: 0; height: 280px; }
        .pd-media img{ object-fit: contain; padding: 14px 0; }
        .pd-media::after{ display: none; }
        .pd-info{ overflow: visible; max-height: none; padding: 22px 20px 26px; }
        .pd-meta-top{ padding-right: 0; }
        .pd-price{ font-size: 1.65rem; }
        .pd-close{ position: sticky; top: 10px; margin: 10px 10px -46px auto; }

        /* bouton d'ajout toujours accessible en bas de la fiche */
        .pd-buy{
            position: sticky;
            bottom: -26px;
            background: var(--cream);
            margin: 4px -20px -26px;
            padding: 14px 20px calc(18px + env(safe-area-inset-bottom));
            border-top: 1px solid rgba(85,16,29,.1);
            box-shadow: 0 -10px 20px -14px rgba(61,11,21,.25);
        }
    }
    @media (max-width: 380px){
        .pd-media{ height: 220px; }
        .pd-delivery{ flex-wrap: wrap; }
        .pd-delivery strong{ margin-left: 0; text-align: left; }
    }
</style>

<div class="modal-overlay" id="orderModalOverlay">
    <div class="pd-panel" id="orderModalPanel" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <button type="button" class="pd-close" id="orderModalClose" aria-label="Fermer">&times;</button>

        <div class="pd-grid">
            {{-- ============ IMAGE (gauche) ============ --}}
            <div class="pd-media">
                <img id="orderBookImage" src="" alt="">
            </div>

            {{-- ============ INFOS (droite) ============ --}}
            <div class="pd-info">
                <div class="pd-meta-top">
                    <span class="book-genre" id="orderBookCategory" style="margin:0;"></span>
                    <span class="pd-seller" id="orderBookSeller"></span>
                </div>

                <h2 class="pd-title" id="orderModalTitle"></h2>
                <p class="pd-author" id="orderBookAuthor"></p>

                <div class="pd-price-row">
                    <strong class="pd-price" id="orderUnitPrice">—</strong>
                    <span class="pd-stock">{{ __('home.order_available_label') }} : <b id="orderAvailableQty">—</b></span>
                </div>

                <div class="pd-badges">
                    <span class="book-genre" id="orderBookCondition" style="background: rgba(233,178,63,.18); color:#8a5f14;"></span>
                    <span class="book-genre" id="orderBookDelivery" style="background: rgba(92,138,55,.14); color:#395e26;"></span>
                    <span class="book-genre" id="orderBookLanguage" style="background: rgba(85,16,29,.08); color: var(--maroon-800);"></span>
                    <span class="book-genre" id="orderBookFormat" style="background: rgba(92,138,55,.1); color: var(--green-700);"></span>
                </div>

                <p class="pd-desc-title">{{ __('home.book_description_label') }}</p>
                <p class="pd-desc" id="orderBookDescription"></p>

                @if($isSeller)
                    <div class="pd-seller-block">
                        <p style="margin:0;">{{ __('home.order_seller_cant_order') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('cart.add') }}" id="orderForm" class="pd-buy">
                        @csrf
                        <input type="hidden" name="book_id" id="orderBookId" value="">

                        <div class="pd-delivery">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="1" y="9" width="14" height="9" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M15 12h3.5L21 15v3h-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="6" cy="19.5" r="1.8" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="17.5" cy="19.5" r="1.8" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            <span>{{ __('home.order_delivery_estimate_label') }}</span>
                            <strong id="orderDeliveryEstimate">—</strong>
                        </div>

                        <div class="pd-buy-row">
                            <span class="pd-label">{{ __('home.order_quantity_label') }}</span>
                            <div class="pd-qty">
                                <div class="order-qty-stepper">
                                    <button type="button" id="orderQtyMinus" aria-label="-">&minus;</button>
                                    <input type="number" name="quantite" id="orderQtyInput" value="1" min="1" step="1" inputmode="numeric">
                                    <button type="button" id="orderQtyPlus" aria-label="+">&plus;</button>
                                </div>
                            </div>
                        </div>

                        <div class="pd-buy-row">
                            <span class="pd-label">{{ __('home.order_total_label') }}</span>
                            <strong class="pd-total" id="orderTotalPrice">—</strong>
                        </div>

                        <button type="submit" class="pd-add" id="orderConfirmButton">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="8" cy="21" r="1" stroke="currentColor" stroke-width="1.8"/>
                                <circle cx="19" cy="21" r="1" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ __('home.cart_add_button') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>