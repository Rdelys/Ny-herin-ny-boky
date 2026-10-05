@php
    $dlOld = [
        'livraison_type' => old('livraison_type', 'standard'),
        'quartier_id' => (string) old('quartier_id', ''),
        'cooperative_id' => (string) old('cooperative_id', ''),
    ];
    $dlText = [
        'free' => __('home.delivery_fee_free'),
        'tbd' => __('home.delivery_fee_tbd'),
        'vipOnlyTana' => __('home.delivery_vip_only_tana'),
        'vipOff' => __('home.delivery_vip_off'),
        'vipLead' => __('home.delivery_vip_unavailable_lead', ['hours' => $delivery['vip']['maxLeadH']]),
        'vipSlots' => __('home.delivery_vip_unavailable_slots'),
        'vipPrecise' => __('home.delivery_vip_precise'),
        'qPlaceholder' => __('home.delivery_quartier_placeholder'),
        'qOther' => __('home.delivery_quartier_other'),
        'qHint' => __('home.delivery_quartier_other_hint'),
        'cPlaceholder' => __('home.delivery_coop_placeholder'),
        'cOther' => __('home.delivery_coop_other'),
        'pa' => __('home.delivery_coop_pa_note'),
        'freeNotice' => __('home.delivery_free_notice'),
    ];
@endphp

<style>
    .dl-notice{
        background: rgba(233,178,63,.14);
        border: 1px dashed var(--gold);
        border-radius: 12px;
        padding: 11px 14px;
        font-size: .84rem;
        color: #6b5a4d;
        line-height: 1.5;
        margin: 0;
    }
    .dl-types{ gap: 10px; }
    .dl-types .modal-radio-card small{ margin-top: 3px; }
    .dl-recap{
        background: rgba(85,16,29,.04);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }
    .dl-row{ display: flex; align-items: baseline; justify-content: space-between; gap: 12px; font-size: .9rem; }
    .dl-row strong{ text-align: right; }
    .dl-total{ padding-top: 10px; border-top: 1px dashed rgba(85,16,29,.18); font-size: 1rem; }
    .dl-total strong{ font-family: var(--serif); font-size: 1.25rem; color: var(--maroon-800); }
    .dl-note{ margin: 0; font-size: .78rem; color: #8a7a6d; line-height: 1.5; }
</style>

<div class="add-book-card">
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

            {{-- ============ LIVRAISON ============ --}}
            <fieldset class="modal-fieldset">
                <legend>{{ __('home.delivery_section_title') }}</legend>

                @if($delivery['longDelay'])
                    <p class="dl-notice">{{ __('home.delivery_long_notice', ['hours' => $delivery['standardMaxH'], 'range' => $delivery['leadLabel']]) }}</p>
                @endif

                <label class="order-reference-field">
                    {{ __('home.order_city_label') }}
                    <select name="ville" id="dlVille" required>
                        <option value="">{{ __('home.order_city_placeholder') }}</option>
                        @foreach($delivery['zones'] as $z)
                            <option value="{{ $z['nom'] }}" @selected(old('ville') === $z['nom'])>{{ $z['nom'] }}</option>
                        @endforeach
                    </select>
                </label>
                @error('ville')<p class="modal-field-error">{{ $message }}</p>@enderror

                {{-- Quartier (Antananarivo) --}}
                <div id="dlQuartierWrap" style="display:none;">
                    <label class="order-reference-field">
                        {{ __('home.delivery_quartier_label') }}
                        <select name="quartier_id" id="dlQuartier" required></select>
                    </label>
                    @error('quartier_id')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <div id="dlQuartierOtherWrap" style="display:none; margin-top:10px;">
                        <label class="order-reference-field">
                            {{ __('home.delivery_quartier_other_label') }}
                            <input type="text" name="quartier_autre" maxlength="120" value="{{ old('quartier_autre') }}" required>
                        </label>
                        @error('quartier_autre')<p class="modal-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Coopérative (autres provinces) --}}
                <div id="dlCoopWrap" style="display:none;">
                    <label class="order-reference-field">
                        {{ __('home.delivery_coop_label') }}
                        <select name="cooperative_id" id="dlCoop" required></select>
                    </label>
                    @error('cooperative_id')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <div id="dlCoopOtherWrap" style="display:none; margin-top:10px;">
                        <label class="order-reference-field">
                            {{ __('home.delivery_coop_other_label') }}
                            <input type="text" name="cooperative_autre" maxlength="120" value="{{ old('cooperative_autre') }}" required>
                        </label>
                        @error('cooperative_autre')<p class="modal-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Type de livraison --}}
                <div class="modal-radio-group dl-types">
                    <label class="modal-radio-card" id="dlStdCard">
                        <input type="radio" name="livraison_type" value="standard" checked>
                        <span><strong>{{ __('home.delivery_standard') }}</strong><small id="dlStdDesc">—</small></span>
                    </label>
                    <label class="modal-radio-card" id="dlVipCard">
                        <input type="radio" name="livraison_type" value="vip">
                        <span>
                            <strong>{{ __('home.delivery_vip') }} <em class="modal-badge-soon">VIP</em></strong>
                            <small id="dlVipDesc">—</small>
                        </span>
                    </label>
                </div>
                @error('livraison_type')<p class="modal-field-error">{{ $message }}</p>@enderror

                {{-- Heure précise VIP --}}
                <div id="dlSlotWrap" style="display:none;">
                    <label class="order-reference-field">
                        {{ __('home.delivery_vip_time_label') }}
                        <select name="heure_prevue" id="dlSlot" required>
                            <option value="">{{ __('home.delivery_vip_time_placeholder') }}</option>
                            @foreach($delivery['vip']['slots'] as $slot)
                                <option value="{{ $slot['value'] }}" @selected(old('heure_prevue') === $slot['value'])>{{ $slot['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    @error('heure_prevue')<p class="modal-field-error">{{ $message }}</p>@enderror
                </div>
            </fieldset>

            {{-- ============ RÉCAP PRIX ============ --}}
            <div class="dl-recap">
                <div class="dl-row"><span>{{ __('home.delivery_subtotal') }}</span><strong id="dlSubtotal">—</strong></div>
                <div class="dl-row"><span>{{ __('home.delivery_fee_label') }}</span><strong id="dlFee">—</strong></div>
                <div class="dl-row"><span>{{ __('home.delivery_estimate') }}</span><strong id="dlEstimate">—</strong></div>
                <div id="dlNotes"></div>
                <div class="dl-row dl-total"><span>{{ __('home.delivery_total_to_pay') }}</span><strong id="dlTotal">—</strong></div>
            </div>

            {{-- ============ PAIEMENT ============ --}}
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

<script>
(function(){
    var form = document.getElementById('checkoutForm');
    if (!form) return;

    var D = @json($delivery);
    var OLD = @json($dlOld);
    var T = @json($dlText);
    var SUBTOTAL = {{ (int) $total }};

    var byId = function(id){ return document.getElementById(id); };
    var fmt = function(n){ return Math.round(n).toLocaleString('fr-FR') + ' Ar'; };

    var villeSel = byId('dlVille');
    var stdRadio = form.querySelector('input[name="livraison_type"][value="standard"]');
    var vipRadio = form.querySelector('input[name="livraison_type"][value="vip"]');
    var vipCard = byId('dlVipCard');
    var stdDesc = byId('dlStdDesc'), vipDesc = byId('dlVipDesc');
    var slotWrap = byId('dlSlotWrap');
    var qWrap = byId('dlQuartierWrap'), qSel = byId('dlQuartier'), qOtherWrap = byId('dlQuartierOtherWrap');
    var cWrap = byId('dlCoopWrap'), cSel = byId('dlCoop'), cOtherWrap = byId('dlCoopOtherWrap');
    var feeEl = byId('dlFee'), estEl = byId('dlEstimate'), totalEl = byId('dlTotal'), notesEl = byId('dlNotes');
    var numberEl = byId('checkoutPaymentNumber'), nameEl = byId('checkoutPaymentName');
    var numberRow = byId('checkoutPaymentNumberRow'), refWrap = byId('checkoutReferenceWrap');
    var refInput = byId('checkoutReference'), submitBtn = byId('checkoutSubmit');
    var cashOption = form.querySelector('[data-payment-option][data-cash="1"]');

    var lastZoneId = null;

    byId('dlSubtotal').textContent = fmt(SUBTOTAL);

    // Affiche un bloc et désactive ses champs quand il est masqué (rien n'est envoyé).
    function toggle(el, on){
        el.style.display = on ? '' : 'none';
        el.querySelectorAll('input,select').forEach(function(c){ c.disabled = !on; });
    }

    function fill(sel, items, placeholder, otherLabel, keep){
        sel.innerHTML = '';
        sel.appendChild(new Option(placeholder, ''));
        items.forEach(function(i){ sel.appendChild(new Option(i.text, i.id)); });
        sel.appendChild(new Option(otherLabel, 'autre'));
        sel.value = keep || '';
    }

    function currentZone(){
        return D.zones.find(function(z){ return z.nom === villeSel.value; }) || null;
    }

    function refresh(first){
        var z = currentZone();
        var isCap = !!z && z.capitale;

        // (re)remplir quartiers / coopératives quand la ville change
        var zid = z ? z.id : null;
        if (zid !== lastZoneId) {
            if (z && z.capitale) {
                fill(qSel, z.quartiers.map(function(q){
                    return { id: q.id, text: D.free ? q.nom : q.nom + ' — ' + fmt(q.frais) };
                }), T.qPlaceholder, T.qOther, first ? OLD.quartier_id : '');
            } else if (z) {
                fill(cSel, z.cooperatives.map(function(c){ return { id: c.id, text: c.nom }; }),
                    T.cPlaceholder, T.cOther, first ? OLD.cooperative_id : '');
            }
            lastZoneId = zid;
        }

        toggle(qWrap, isCap);
        toggle(cWrap, !!z && !isCap);
        toggle(qOtherWrap, isCap && qSel.value === 'autre');
        toggle(cOtherWrap, !!z && !isCap && cSel.value === 'autre');

        // disponibilité du VIP
        var vip = D.vip, vipReason = '';
        if (!z || !isCap) vipReason = T.vipOnlyTana;
        else if (!vip.enabled) vipReason = T.vipOff;
        else if (!vip.leadOk) vipReason = T.vipLead;
        else if (!vip.slots.length) vipReason = T.vipSlots;
        var vipOk = vipReason === '';

        vipRadio.disabled = !vipOk;
        vipCard.classList.toggle('is-disabled', !vipOk);
        if (!vipOk && vipRadio.checked) stdRadio.checked = true;

        stdDesc.textContent = z ? z.label : '';
        vipDesc.textContent = vipOk
            ? vip.label + ' · ' + T.vipPrecise + ' · +' + fmt(vip.surcharge)
            : vipReason;

        var isVip = vipRadio.checked && vipOk;
        toggle(slotWrap, isVip);

        // frais (aperçu : le serveur recalcule tout)
        var fee = 0, tbd = false, pa = false;
        if (z) {
            if (isCap) {
                if (qSel.value === 'autre') { tbd = !D.free; }
                else {
                    var q = z.quartiers.find(function(x){ return String(x.id) === qSel.value; });
                    fee = q ? q.frais : 0;
                }
            } else {
                fee = z.frais;
                pa = true;
            }
            if (D.free) fee = 0;
            if (isVip) fee += vip.surcharge;
        }

        if (!z) feeEl.textContent = '—';
        else if (fee === 0 && D.free) feeEl.textContent = T.free;
        else if (fee === 0 && tbd) feeEl.textContent = T.tbd;
        else feeEl.textContent = fmt(fee) + (tbd ? ' + ' + T.tbd : '');

        estEl.textContent = !z ? '—' : (isVip ? vip.label : z.label);
        totalEl.textContent = fmt(SUBTOTAL + fee);

        // notes
        var notes = [];
        if (D.free) notes.push(T.freeNotice);
        if (isCap && qSel.value === 'autre' && !D.free) notes.push(T.qHint);
        if (pa) notes.push(T.pa);
        notesEl.innerHTML = '';
        notes.forEach(function(text){
            var p = document.createElement('p');
            p.className = 'dl-note';
            p.textContent = text;
            notesEl.appendChild(p);
        });

        // paiement : espèces seulement dans la capitale ; numéro + référence masqués pour espèces
        if (cashOption) {
            cashOption.style.display = isCap ? '' : 'none';
            var cashRadio = cashOption.querySelector('input[type="radio"]');
            if (!isCap && cashRadio.checked) {
                var fb = form.querySelector('[data-payment-option][data-cash="0"] input[type="radio"]');
                if (fb) fb.checked = true;
            }
        }
        var checked = form.querySelector('input[name="mode_paiement"]:checked');
        var isCash = !!checked && checked.value === 'especes';
        numberEl.textContent = checked ? (checked.getAttribute('data-payment-number') || '—') : '—';
        nameEl.textContent = checked ? (checked.getAttribute('data-payment-name') || '—') : '—';
        numberRow.style.display = isCash ? 'none' : '';
        refWrap.style.display = isCash ? 'none' : '';
        refInput.required = !isCash;
    }

    villeSel.addEventListener('change', function(){ refresh(false); });
    qSel.addEventListener('change', function(){ refresh(false); });
    cSel.addEventListener('change', function(){ refresh(false); });
    form.querySelectorAll('input[name="livraison_type"], input[name="mode_paiement"]').forEach(function(r){
        r.addEventListener('change', function(){ refresh(false); });
    });

    form.addEventListener('submit', function(){
        if (submitBtn) submitBtn.disabled = true;
    });

    if (OLD.livraison_type === 'vip') vipRadio.checked = true;
    refresh(true);
})();
</script>