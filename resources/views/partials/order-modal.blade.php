{{-- ============ MODAL DE COMMANDE ============ --}}
{{-- Ouverte depuis les boutons "aperçu rapide" / "ajouter" des cartes livres,
     partout où elles apparaissent (accueil, catalogue, fiche vendeur).
     Les données du livre sont lues depuis les attributs data-* du bouton
     cliqué (voir openOrderModal() dans layouts/app.blade.php).

     Le formulaire est un vrai POST vers orders.store : la commande est
     enregistrée en base avec le statut « en attente de livraison », et le
     client la retrouve sur son profil.

     Les numéros mobile money ET le nom du titulaire de la puce viennent des
     paramètres admin (Setting::paymentAccounts()) : l'admin les modifie
     depuis /admin/parametres, sans toucher au code.

     Le numéro et le nom du titulaire sont toujours affichés dès qu'un
     opérateur est configuré côté admin, quel que soit le statut du
     visiteur. Le formulaire de commande (bouton "Confirmer", champ
     référence...) reste lui réservé aux personnes connectées avec un
     compte CLIENT (pas vendeur, pas invité) : sinon on affiche une
     invitation à se connecter / s'inscrire à la place. --}}
@php
    $canOrder = auth()->check() && auth()->user()->isClient();
    $paymentAccounts = \App\Models\Setting::paymentAccounts();
    // On ne propose que les opérateurs dont le numéro est renseigné en admin.
    $paymentAccounts = array_filter($paymentAccounts, fn ($a) => $a['numero'] !== '');
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
                    <span class="book-genre" id="orderBookCondition" style="margin:0; background: rgba(233,178,63,.18); color:#8a5f14;"></span>
                </div>
            </div>
        </div>

        <p class="order-book-description" id="orderBookDescription"></p>

        @if($canOrder && count($paymentAccounts))
            <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                @csrf
                <input type="hidden" name="book_id" id="orderBookId" value="">
        @endif

        <div class="order-summary">
            <div class="order-summary-row">
                <span>{{ __('home.order_unit_price') }}</span>
                <strong id="orderUnitPrice">—</strong>
            </div>

            <div class="order-summary-row">
                <span>{{ __('home.order_available_label') }}</span>
                <strong id="orderAvailableQty">—</strong>
            </div>

            @if($canOrder && count($paymentAccounts))
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
            @endif
        </div>

        @if(count($paymentAccounts))
            {{-- Numéro ET nom du titulaire de la puce : toujours visibles dès
                 qu'un opérateur est configuré côté admin, même pour un
                 visiteur non connecté (pas de formulaire de commande dans ce
                 cas, mais on renseigne déjà où envoyer l'argent). --}}
            <fieldset class="modal-fieldset">
                <legend>{{ __('home.order_payment_legend') }}</legend>
                <div class="modal-radio-group order-payment-group">
                    @foreach($paymentAccounts as $key => $account)
                        <label class="modal-radio-card">
                            <input type="radio" name="mode_paiement" value="{{ $key }}"
                                data-payment-number="{{ $account['numero'] }}"
                                data-payment-name="{{ $account['nom'] }}"
                                @checked($loop->first)>
                            <span><strong>{{ $account['label'] }}</strong></span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="order-payment-number">
                <div>
                    <span>{{ __('home.order_payment_number_label') }}</span>
                    <strong id="orderPaymentNumber">—</strong>
                </div>
                <div class="order-payment-owner">
                    <span>{{ __('home.order_payment_name_label') }}</span>
                    <strong id="orderPaymentName">—</strong>
                </div>
            </div>
        @endif

        @if($canOrder && count($paymentAccounts))
            {{-- ---- client connecté : formulaire de commande complet ---- --}}
            <label class="order-reference-field">
                {{ __('home.order_payment_reference_label') }}
                <input type="text" name="reference_paiement" id="orderPaymentReference" maxlength="80" placeholder="{{ __('home.order_payment_reference_placeholder') }}">
            </label>
            <p class="modal-field-error" id="orderPaymentReferenceError" style="display:none;">{{ __('home.order_payment_reference_error') }}</p>

            <button type="submit" class="btn-modal-primary" id="orderConfirmButton">{{ __('home.order_confirm_button') }}</button>
            <p class="order-static-note">{{ __('home.order_pending_note') }}</p>
            </form>
        @elseif($canOrder)
            {{-- client connecté mais aucun numéro de paiement configuré côté admin --}}
            <div class="order-login-prompt">
                <p>{{ __('home.order_no_payment_account') }}</p>
            </div>
        @else
            {{-- ---- invité, ou connecté en tant que vendeur : invitation à se connecter ---- --}}
            <div class="order-login-prompt">
                <p>
                    @auth
                        {{ __('home.order_seller_cant_order') }}
                    @else
                        {{ __('home.order_guest_prompt') }}
                    @endauth
                </p>
                <div class="order-login-actions">
                    <button type="button" class="order-login-btn order-login-btn-ghost" data-auth-open="login">{{ __('home.nav_login') }}</button>
                    <button type="button" class="order-login-btn order-login-btn-primary" data-auth-open="registerClient">{{ __('home.auth_signup_as_client') }}</button>
                </div>
            </div>
        @endif
    </div>
</div>
