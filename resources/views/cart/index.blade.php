@extends('layouts.app')

@section('meta_title', __('home.cart_title') . ' — ' . config('app.name'))
@section('meta_robots', 'noindex, nofollow')

@section('content')
    @php
        $guestSuccess = session('guest_order_success');
    @endphp

    <section>
        <div class="wrap" style="max-width: 860px;">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.cart_title') }}</h2>
                    @if(! $guestSuccess && $lines->isNotEmpty())
                        <p>{{ __('home.cart_items_count', ['count' => $lines->sum('quantite')]) }}</p>
                    @endif
                </div>
                <a href="{{ route('books.index') }}" class="see-all">{{ __('home.cart_browse') }}</a>
            </div>

            @if(session('success'))
                <p class="flash-success">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p class="flash-error">{{ session('error') }}</p>
            @endif

            @if($guestSuccess)
                {{-- Confirmation invité (il n'a pas de profil où être redirigé). --}}
                <div class="order-guest-success">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2 class="modal-title" style="margin-top:14px;">{{ __('home.order_guest_success_title') }}</h2>
                    <p>{{ $guestSuccess['message'] }}</p>
                    <p class="order-guest-reference">{{ $guestSuccess['groupe'] }}</p>

                    @if(! empty($guestSuccess['invoice_url']))
                        <a href="{{ $guestSuccess['invoice_url'] }}" target="_blank" class="btn btn-primary" style="margin: 8px auto 0;">
                            {{ __('home.order_download_invoice') }}
                        </a>
                    @endif

                    <div style="margin-top:14px;">
                        @foreach($guestSuccess['orders'] as $o)
                            <span class="seller-table-sub" style="display:block;">{{ $o['titre'] }} · {{ $o['reference'] }}</span>
                        @endforeach
                    </div>

                    <p class="order-static-note">{{ __('home.order_guest_success_hint') }}</p>
                </div>

            @elseif($lines->isEmpty())
                <p style="color:#7a6a5d;">{{ __('home.cart_empty') }}</p>

            @else
                {{-- ============ LIGNES DU PANIER ============ --}}
                <div class="cart-lines">
                    @foreach($lines as $line)
                        <div class="cart-line {{ $line->disponible ? '' : 'is-unavailable' }}">
                            <div class="cart-line-cover">
                                <img src="{{ $line->book->image_path ? asset('storage/'.$line->book->image_path) : 'https://picsum.photos/seed/nhb-book-'.$line->book->id.'/500/667' }}" alt="{{ $line->book->titre }}">
                            </div>

                            <div class="cart-line-info">
                                <strong class="cart-line-title">{{ $line->book->titre }}</strong>
                                <span class="cart-line-sub">{{ $line->book->seller->sellerProfile->nom_entreprise ?? $line->book->seller->name }}</span>
                                <span class="cart-line-sub">{{ number_format($line->prix_unitaire, 0, ',', ' ') }} Ar</span>
                                @unless($line->disponible)
                                    <span class="modal-field-error" style="margin:4px 0 0;">{{ __('home.order_error_stock', ['quantite' => $line->book->quantite]) }}</span>
                                @endunless
                            </div>

                            <form method="POST" action="{{ route('cart.update', $line->book->id) }}" class="cart-line-qty">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantite" value="{{ $line->quantite }}" min="1" max="{{ max(1, $line->book->quantite) }}" onchange="this.form.submit()" aria-label="{{ __('home.order_quantity_label') }}">
                            </form>

                            <strong class="cart-line-total">{{ number_format($line->total, 0, ',', ' ') }} Ar</strong>

                            <form method="POST" action="{{ route('cart.remove', $line->book->id) }}" class="cart-line-remove">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="table-action-link table-action-danger">{{ __('home.cart_remove') }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="cart-summary">
                    <span>{{ __('home.order_total_label') }}</span>
                    <strong>{{ number_format($total, 0, ',', ' ') }} Ar</strong>
                </div>

                {{-- ============ FINALISER LA COMMANDE ============ --}}
                <div class="add-book-card" style="max-width:none;">
                    <h3 class="add-book-title">{{ __('home.cart_checkout_title') }}</h3>

                    @if(! count($paymentAccounts))
                        <p style="color:#7a6a5d;">{{ __('home.order_no_payment_account') }}</p>
                    @else
                        <form method="POST" action="{{ route('orders.store') }}" id="checkoutForm" class="modal-form">
                            @csrf

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
                                        <a href="#" data-auth-switch="registerClient">{{ __('home.order_guest_account_link') }}</a>
                                    </p>
                                </fieldset>
                            @endguest

                            <label class="order-reference-field">
                                {{ __('home.order_guest_address_label') }}
                                <input type="text" name="adresse_livraison" value="{{ old('adresse_livraison') }}" placeholder="{{ __('home.order_guest_address_placeholder') }}" required>
                            </label>
                            @error('adresse_livraison')<p class="modal-field-error">{{ $message }}</p>@enderror

                            <fieldset class="modal-fieldset">
                                <legend>{{ __('home.order_payment_legend') }}</legend>
                                <div class="modal-radio-group order-payment-group">
                                    @foreach($paymentAccounts as $key => $account)
                                        <label class="modal-radio-card" data-payment-option data-cash="0">
                                            <input type="radio" name="mode_paiement" value="{{ $key }}"
                                                data-payment-number="{{ $account['numero'] }}"
                                                data-payment-name="{{ $account['nom'] }}"
                                                @checked(old('mode_paiement', $loop->first ? $key : null) === $key)>
                                            <span><strong>{{ $account['label'] }}</strong></span>
                                        </label>
                                    @endforeach

                                    <label class="modal-radio-card" data-payment-option data-cash="1" style="display:none;">
                                        <input type="radio" name="mode_paiement" value="especes"
                                            data-payment-number="" data-payment-name=""
                                            @checked(old('mode_paiement') === 'especes')>
                                        <span><strong>{{ __('home.order_payment_cash') }}</strong>
                                            <small>{{ __('home.order_payment_cash_hint') }}</small>
                                        </span>
                                    </label>
                                </div>
                            </fieldset>
                            @error('mode_paiement')<p class="modal-field-error">{{ $message }}</p>@enderror

                            <div class="order-payment-number" id="checkoutPaymentNumberRow">
                                <div>
                                    <span>{{ __('home.order_payment_number_label') }}</span>
                                    <strong id="checkoutPaymentNumber">—</strong>
                                </div>
                                <div class="order-payment-owner">
                                    <span>{{ __('home.order_payment_name_label') }}</span>
                                    <strong id="checkoutPaymentName">—</strong>
                                </div>
                            </div>

                            <label class="order-reference-field">
                                {{ __('home.order_city_label') }}
                                <select name="ville" id="checkoutVille" required>
                                    <option value="">{{ __('home.order_city_placeholder') }}</option>
                                    @foreach($villes as $ville)
                                        <option value="{{ $ville }}" data-cash-allowed="{{ $ville === $villeEspeces ? '1' : '0' }}" @selected(old('ville') === $ville)>{{ $ville }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('ville')<p class="modal-field-error">{{ $message }}</p>@enderror

                            <div id="checkoutReferenceWrap">
                                <label class="order-reference-field">
                                    {{ __('home.order_payment_reference_label') }}
                                    <input type="text" name="reference_paiement" id="checkoutReference" maxlength="80" value="{{ old('reference_paiement') }}" placeholder="{{ __('home.order_payment_reference_placeholder') }}">
                                </label>
                                @error('reference_paiement')<p class="modal-field-error">{{ $message }}</p>@enderror
                            </div>

                            @unless($canCheckout)
                                <p class="modal-field-error">{{ __('home.cart_unavailable') }}</p>
                            @endunless

                            <button type="submit" class="btn-modal-primary" id="checkoutSubmit" @disabled(! $canCheckout)>{{ __('home.order_confirm_button') }}</button>
                            <p class="order-static-note">{{ __('home.order_pending_note') }}</p>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function(){
        var form = document.getElementById('checkoutForm');
        if (!form) return;

        var villeSelect = document.getElementById('checkoutVille');
        var numberEl = document.getElementById('checkoutPaymentNumber');
        var nameEl = document.getElementById('checkoutPaymentName');
        var numberRow = document.getElementById('checkoutPaymentNumberRow');
        var refWrap = document.getElementById('checkoutReferenceWrap');
        var refInput = document.getElementById('checkoutReference');
        var submitBtn = document.getElementById('checkoutSubmit');
        var cashOption = form.querySelector('[data-payment-option][data-cash="1"]');

        function selected(){
            return form.querySelector('input[name="mode_paiement"]:checked');
        }

        // Numéro à contacter + champ référence : masqués pour "Espèces".
        function refresh(){
            var checked = selected();
            var isCash = !!checked && checked.value === 'especes';

            numberEl.textContent = checked ? (checked.getAttribute('data-payment-number') || '—') : '—';
            nameEl.textContent = checked ? (checked.getAttribute('data-payment-name') || '—') : '—';
            numberRow.style.display = isCash ? 'none' : '';
            refWrap.style.display = isCash ? 'none' : '';
            refInput.required = !isCash;
        }

        // "Espèces" n'apparaît que pour la ville qui l'autorise.
        function updateCash(){
            var option = villeSelect.options[villeSelect.selectedIndex];
            var allowed = !!option && option.getAttribute('data-cash-allowed') === '1';

            if (cashOption) {
                cashOption.style.display = allowed ? '' : 'none';
                var radio = cashOption.querySelector('input[type="radio"]');
                if (!allowed && radio.checked) {
                    var fallback = form.querySelector('[data-payment-option][data-cash="0"] input[type="radio"]');
                    if (fallback) fallback.checked = true;
                }
            }
            refresh();
        }

        form.querySelectorAll('input[name="mode_paiement"]').forEach(function(radio){
            radio.addEventListener('change', refresh);
        });
        villeSelect.addEventListener('change', updateCash);

        form.addEventListener('submit', function(){
            if (submitBtn) submitBtn.disabled = true;
        });

        updateCash();
    })();
</script>
@endpush