@php
    $isSeller = auth()->check() && auth()->user()->isSeller();
    $isGuestOrClient = ! $isSeller;
    $paymentAccounts = \App\Models\Setting::paymentAccounts();
    $paymentAccounts = array_filter($paymentAccounts, fn ($a) => $a['numero'] !== '');
    $villes = \App\Models\Setting::VILLES;
    $villeEspeces = \App\Models\Setting::VILLE_ESPECES;
    $guestSuccess = session('guest_order_success');
@endphp

<div class="modal-overlay" id="orderModalOverlay">
    <div class="modal-panel modal-panel--wide" id="orderModalPanel" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <button type="button" class="modal-close" id="orderModalClose" aria-label="Fermer">&times;</button>

        @if($guestSuccess)
            {{-- Confirmation affichée juste après une commande invité (pas de
                 profil où rediriger, donc on confirme directement ici). --}}
            <div class="order-guest-success">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h2 class="modal-title" style="margin-top:14px;">{{ __('home.order_guest_success_title') }}</h2>
                <p>{{ $guestSuccess['message'] }}</p>
                <p class="order-guest-reference">{{ $guestSuccess['reference'] }}</p>
                @if(!empty($guestSuccess['invoice_url']))
                    <a href="{{ $guestSuccess['invoice_url'] }}" target="_blank" class="btn btn-primary" style="margin: 8px auto 0;">
                        {{ __('home.order_download_invoice') }}
                    </a>
                @endif
                <p class="order-static-note">{{ __('home.order_guest_success_hint') }}</p>
            </div>
        @else
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
                        <span class="book-genre" id="orderBookDelivery" style="margin:0; background: rgba(92,138,55,.14); color:#395e26;"></span>
                    </div>
                </div>
            </div>

            <p class="order-book-description" id="orderBookDescription"></p>

            @if($isGuestOrClient && count($paymentAccounts))
                <form method="POST" action="{{ route('orders.store') }}" id="orderForm" class="modal-form">
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
                <div class="order-summary-row">
                    <span>{{ __('home.order_delivery_estimate_label') }}</span>
                    <strong id="orderDeliveryEstimate">—</strong>
                </div>

                @if($isGuestOrClient && count($paymentAccounts))
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

            @if($isGuestOrClient && count($paymentAccounts))
                @guest
                    <fieldset class="modal-fieldset">
                        <legend>{{ __('home.order_guest_legend') }}</legend>
                        <div class="modal-form-row">
                            <label>{{ __('home.order_guest_name_label') }}
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" required>
                            </label>
                            <label>{{ __('home.order_guest_phone_label') }}
                                <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" placeholder="034 xx xxx xx" required>
                            </label>
                        </div>
                        @error('guest_name')<p class="modal-field-error">{{ $message }}</p>@enderror
                        @error('guest_phone')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <label>{{ __('home.order_guest_email_label') }}
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" placeholder="{{ __('home.order_guest_email_placeholder') }}">
                        </label>
                        @error('guest_email')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <p class="field-hint">{{ __('home.order_guest_account_hint') }}
                            <a href="#" data-auth-switch="registerClient" data-order-to-auth>{{ __('home.order_guest_account_link') }}</a>
                        </p>
                    </fieldset>
                @endguest

                {{-- Adresse de livraison : demandée à TOUT le monde, connecté ou non. --}}
                <label class="order-reference-field">
                    {{ __('home.order_guest_address_label') }}
                    <input type="text" name="adresse_livraison" value="{{ old('adresse_livraison') }}" placeholder="{{ __('home.order_guest_address_placeholder') }}" required>
                </label>
                @error('adresse_livraison')<p class="modal-field-error">{{ $message }}</p>@enderror
            @endif

            @if(count($paymentAccounts))
                <fieldset class="modal-fieldset">
                    <legend>{{ __('home.order_payment_legend') }}</legend>
                    <div class="modal-radio-group order-payment-group" id="orderPaymentGroup">
                        @foreach($paymentAccounts as $key => $account)
                            <label class="modal-radio-card" data-payment-option data-cash="0">
                                <input type="radio" name="mode_paiement" value="{{ $key }}"
                                    data-payment-number="{{ $account['numero'] }}"
                                    data-payment-name="{{ $account['nom'] }}"
                                    @checked($loop->first)>
                                <span><strong>{{ $account['label'] }}</strong></span>
                            </label>
                        @endforeach

                        <label class="modal-radio-card" data-payment-option data-cash="1" style="display:none;">
                            <input type="radio" name="mode_paiement" value="especes"
                                data-payment-number="" data-payment-name="">
                            <span><strong>{{ __('home.order_payment_cash') }}</strong>
                                <small>{{ __('home.order_payment_cash_hint') }}</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <div class="order-payment-number" id="orderPaymentNumberRow">
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

            @if($isGuestOrClient && count($paymentAccounts))
                <label class="order-reference-field">
                    {{ __('home.order_city_label') }}
                    <select name="ville" id="orderVilleSelect" required>
                        <option value="">{{ __('home.order_city_placeholder') }}</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville }}" data-cash-allowed="{{ $ville === $villeEspeces ? '1' : '0' }}">{{ $ville }}</option>
                        @endforeach
                    </select>
                </label>
                @error('ville')<p class="modal-field-error">{{ $message }}</p>@enderror

                <div id="orderReferenceWrap">
                    <label class="order-reference-field">
                        {{ __('home.order_payment_reference_label') }}
                        <input type="text" name="reference_paiement" id="orderPaymentReference" maxlength="80" placeholder="{{ __('home.order_payment_reference_placeholder') }}">
                    </label>
                    <p class="modal-field-error" id="orderPaymentReferenceError" style="display:none;">{{ __('home.order_payment_reference_error') }}</p>
                </div>

                <button type="submit" class="btn-modal-primary" id="orderConfirmButton">{{ __('home.order_confirm_button') }}</button>
                <p class="order-static-note">{{ __('home.order_pending_note') }}</p>
                </form>
            @elseif($isGuestOrClient)
                <div class="order-login-prompt">
                    <p>{{ __('home.order_no_payment_account') }}</p>
                </div>
            @else
                {{-- vendeur connecté : ne peut jamais commander --}}
                <div class="order-login-prompt">
                    <p>{{ __('home.order_seller_cant_order') }}</p>
                </div>
            @endif
        @endif
    </div>
</div>