{{-- ============ MODAL DE COMMANDE (statique pour l'instant) ============ --}}
{{-- Ouverte depuis les boutons "aperçu rapide" / "ajouter" des cartes livres,
     partout où elles apparaissent (accueil, catalogue, fiche vendeur).
     Les données du livre sont lues depuis les attributs data-* du bouton
     cliqué (voir openOrderModal() dans layouts/app.blade.php) — pas d'appel
     serveur pour l'instant, tout est calculé côté client. --}}

<div class="modal-overlay" id="orderModalOverlay">
    <div class="modal-panel" id="orderModalPanel" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <button type="button" class="modal-close" id="orderModalClose" aria-label="Fermer">&times;</button>

        <div class="order-book">
            <div class="order-book-cover">
                <img id="orderBookImage" src="" alt="">
            </div>
            <div>
                <h2 class="modal-title" id="orderModalTitle" style="text-align:left; margin-bottom:2px;"></h2>
                <p class="order-book-seller" id="orderBookSeller"></p>
            </div>
        </div>

        <div class="order-summary">
            <div class="order-summary-row">
                <span>{{ __('home.order_unit_price') }}</span>
                <strong id="orderUnitPrice">—</strong>
            </div>

            <div class="order-summary-row">
                <span>{{ __('home.order_quantity_label') }}</span>
                <div class="order-qty-stepper">
                    <button type="button" id="orderQtyMinus" aria-label="-">&minus;</button>
                    <input type="number" id="orderQtyInput" value="1" min="1" step="1" inputmode="numeric">
                    <button type="button" id="orderQtyPlus" aria-label="+">&plus;</button>
                </div>
            </div>

            <div class="order-summary-row order-summary-total">
                <span>{{ __('home.order_total_label') }}</span>
                <strong id="orderTotalPrice">—</strong>
            </div>
        </div>

        <fieldset class="modal-fieldset">
            <legend>{{ __('home.order_payment_legend') }}</legend>
            <div class="modal-radio-group order-payment-group">
                <label class="modal-radio-card">
                    <input type="radio" name="order_payment" value="mvola" checked>
                    <span><strong>MVola</strong></span>
                </label>
                <label class="modal-radio-card">
                    <input type="radio" name="order_payment" value="orange">
                    <span><strong>Orange Money</strong></span>
                </label>
                <label class="modal-radio-card">
                    <input type="radio" name="order_payment" value="airtel">
                    <span><strong>Airtel Money</strong></span>
                </label>
            </div>
        </fieldset>

        <div class="order-payment-number">
            <span>{{ __('home.order_payment_number_label') }}</span>
            <strong id="orderPaymentNumber">034 41 266 44</strong>
        </div>

        <button type="button" class="btn-modal-primary" id="orderConfirmButton" data-confirmed-label="{{ __('home.order_confirmed_label') }}">{{ __('home.order_confirm_button') }}</button>
        <p class="order-static-note">{{ __('home.order_static_note') }}</p>
    </div>
</div>