@php
    $isSeller = auth()->check() && auth()->user()->isSeller();
@endphp

<div class="modal-overlay" id="orderModalOverlay">
    <div class="modal-panel modal-panel--wide" id="orderModalPanel" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <button type="button" class="modal-close" id="orderModalClose" aria-label="Fermer">&times;</button>

        <div class="order-book">
            <div class="order-book-cover">
                <img id="orderBookImage" src="" alt="">
            </div>
            <div>
                <h2 class="modal-title" id="orderModalTitle" style="text-align:left; margin-bottom:2px;"></h2>
                <p class="order-book-seller" id="orderBookSeller"></p>
                <p class="order-book-author" id="orderBookAuthor"></p>
                <div class="order-book-badges">
                    <span class="book-genre" id="orderBookCategory" style="margin:0;"></span>
orderBookLanguage                     <span class="book-genre" id="orderBookCondition" style="margin:0; background: rgba(233,178,63,.18); color:#8a5f14;"></span>
                    <span class="book-genre" id="orderBookDelivery" style="margin:0; background: rgba(92,138,55,.14); color:#395e26;"></span>
                    <span class="book-genre" id="orderBookLanguage" style="margin:0; background: rgba(85,16,29,.08); color: var(--maroon-800);"></span>
                    <span class="book-genre" id="orderBookFormat" style="margin:0; background: rgba(92,138,55,.1); color: var(--green-700);"></span>
                </div>
            </div>
        </div>

        <p class="order-book-description" id="orderBookDescription"></p>

        @if($isSeller)
            {{-- vendeur connecté : ne peut jamais commander --}}
            <div class="order-login-prompt">
                <p>{{ __('home.order_seller_cant_order') }}</p>
            </div>
        @else
            <form method="POST" action="{{ route('cart.add') }}" id="orderForm" class="modal-form">
                @csrf
                <input type="hidden" name="book_id" id="orderBookId" value="">

                <div class="order-summary">
                    <div class="order-summary-row">
                        <span>{{ __('home.order_unit_price') }}</span>
                        <strong id="orderUnitPrice">—</strong>
                    </div>
                    <div class="order-summary-row">
                        <span>{{ __('home.order_available_label') }}</span>
                        <strong id="orderAvailableQty">—</strong>
                    </div>
                    <div class="order-summary-row">
                        <span>{{ __('home.order_delivery_estimate_label') }}</span>
                        <strong id="orderDeliveryEstimate">—</strong>
                    </div>
                    <div class="order-summary-row">
                        <span>{{ __('home.order_quantity_label') }}</span>
                        <div class="order-qty-stepper">
                            <button type="button" id="orderQtyMinus" aria-label="-">&minus;</button>
                            <input type="number" name="quantite" id="orderQtyInput" value="1" min="1" step="1" inputmode="numeric">
                            <button type="button" id="orderQtyPlus" aria-label="+">&plus;</button>
                        </div>
                    </div>
                    <div class="order-summary-row order-summary-total">
                        <span>{{ __('home.order_total_label') }}</span>
                        <strong id="orderTotalPrice">—</strong>
                    </div>
                </div>

                <button type="submit" class="btn-modal-primary" id="orderConfirmButton">{{ __('home.cart_add_button') }}</button>
            </form>
        @endif
    </div>
</div>