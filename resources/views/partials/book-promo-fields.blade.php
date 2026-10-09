@php
    $promoBook   = $book ?? null;
    $promoType   = old('promo_type', $promoBook->promo_type ?? '');
    $promoValeur = old('promo_valeur', $promoBook->promo_valeur ?? '');
@endphp

<fieldset class="modal-fieldset">
    <legend>{{ __('home.book_promo_legend') }}</legend>
    <p class="field-hint" style="margin:0 0 12px;">{{ __('home.book_promo_hint') }}</p>

    <div class="modal-form-row">
        <label>{{ __('home.book_promo_type_label') }}
            <select name="promo_type" id="promoTypeInput">
                <option value="">{{ __('home.book_promo_none') }}</option>
                <option value="percent" @selected($promoType === 'percent')>{{ __('home.book_promo_percent') }}</option>
                <option value="amount" @selected($promoType === 'amount')>{{ __('home.book_promo_amount') }}</option>
            </select>
        </label>
        <label>{{ __('home.book_promo_value_label') }}
            <input type="number" name="promo_valeur" id="promoValeurInput" min="1" step="1"
                   inputmode="numeric" value="{{ $promoValeur }}">
        </label>
    </div>
    @error('promo_type')<p class="modal-field-error">{{ $message }}</p>@enderror
    @error('promo_valeur')<p class="modal-field-error">{{ $message }}</p>@enderror
    <p class="field-hint" id="promoClientHint"></p>
</fieldset>

<script>
    (function(){
        var TIERS = @json(
            collect(\App\Models\Setting::commissionTiers())->map(function ($t) {
                return ['max' => $t['max'], 'rate' => $t['rate'] / 100];
            })->values()
        );
        var LABEL   = @json(__('home.book_promo_client_hint'));
        var INSTEAD = @json(__('home.book_promo_instead_of'));
        var MAX_PERCENT = {{ \App\Models\Book::PROMO_MAX_PERCENT }};

        function rateFor(m){
            m = Number(m) || 0;
            for (var i = 0; i < TIERS.length; i++) {
                if (TIERS[i].max === null || m <= TIERS[i].max) return TIERS[i].rate;
            }
            return TIERS.length ? TIERS[TIERS.length - 1].rate : 0;
        }
        function fmt(n){ return Math.round(n).toLocaleString('fr-FR') + ' Ar'; }

        var prix = document.getElementById('prixAchatInput');
        var type = document.getElementById('promoTypeInput');
        var val  = document.getElementById('promoValeurInput');
        var hint = document.getElementById('promoClientHint');
        if (!prix || !type || !val || !hint) return;

        function toggle(){
            val.disabled = !type.value;
            if (type.value === 'percent') {
                val.max = MAX_PERCENT;
                val.placeholder = '10';
            } else {
                val.removeAttribute('max');
                val.placeholder = type.value === 'amount' ? '1000' : '';
            }
        }

        function update(){
            toggle();
            var base = parseFloat(prix.value);
            var v = parseFloat(val.value);
            if (!type.value || !base || base <= 0 || !v || v <= 0) { hint.textContent = ''; return; }

            var promo = type.value === 'percent' ? base * (1 - v / 100) : base - v;
            if (promo < 0) promo = 0;
            if (promo >= base) { hint.textContent = ''; return; }

            var avant = base  * (1 + rateFor(base));
            var apres = promo * (1 + rateFor(promo));
            hint.textContent = LABEL + ' ' + fmt(apres) + ' (' + INSTEAD + ' ' + fmt(avant) + ')';
        }

        type.addEventListener('change', update);
        val.addEventListener('input', update);
        prix.addEventListener('input', update);
        update();
    })();
</script>