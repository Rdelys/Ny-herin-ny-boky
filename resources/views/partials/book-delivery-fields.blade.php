@php
    $delaiBook   = $book ?? null;
    $delaiUnite  = old('delai_livraison_unite', $delaiBook->delai_livraison_unite ?? 'jours');
    $delaiMin    = old('delai_livraison_min', $delaiBook->delai_livraison_min ?? 1);
    $delaiMax    = old('delai_livraison_max', $delaiBook->delai_livraison_max ?? 1);
    $delaiHeures = $delaiUnite === 'heures';
    $delaiLimit  = $delaiHeures ? \App\Models\Book::DELAI_MAX_HEURES : \App\Models\Book::DELAI_MAX_JOURS;
@endphp

<fieldset class="modal-fieldset">
    <legend>{{ __('home.book_delivery_legend') }}</legend>
    <p class="field-hint" id="delaiHint" style="margin:0 0 12px;">
        {{ $delaiHeures ? __('home.book_delivery_hint_hours') : __('home.book_delivery_hint') }}
    </p>

    <label>{{ __('home.book_delivery_unit_label') }}
        <select name="delai_livraison_unite" id="delaiUniteInput" required>
            <option value="heures" @selected($delaiHeures)>{{ __('home.book_delivery_unit_hours') }}</option>
            <option value="jours" @selected(! $delaiHeures)>{{ __('home.book_delivery_unit_days') }}</option>
        </select>
    </label>
    @error('delai_livraison_unite')<p class="modal-field-error">{{ $message }}</p>@enderror

    <div class="modal-form-row">
        <label><span id="delaiMinLabel">{{ $delaiHeures ? __('home.book_delivery_min_hours_label') : __('home.book_delivery_min_label') }}</span>
            <input type="number" name="delai_livraison_min" id="delaiMinInput" min="1" step="1"
                   max="{{ $delaiLimit }}" value="{{ $delaiMin }}" inputmode="numeric" required>
        </label>
        <label><span id="delaiMaxLabel">{{ $delaiHeures ? __('home.book_delivery_max_hours_label') : __('home.book_delivery_max_label') }}</span>
            <input type="number" name="delai_livraison_max" id="delaiMaxInput" min="1" step="1"
                   max="{{ $delaiLimit }}" value="{{ $delaiMax }}" inputmode="numeric" required>
        </label>
    </div>
    @error('delai_livraison_min')<p class="modal-field-error">{{ $message }}</p>@enderror
    @error('delai_livraison_max')<p class="modal-field-error">{{ $message }}</p>@enderror
</fieldset>

<script>
    (function(){
        var LIMITS = {
            heures: {{ \App\Models\Book::DELAI_MAX_HEURES }},
            jours: {{ \App\Models\Book::DELAI_MAX_JOURS }}
        };
        var TEXTS = {
            heures: {
                min: @json(__('home.book_delivery_min_hours_label')),
                max: @json(__('home.book_delivery_max_hours_label')),
                hint: @json(__('home.book_delivery_hint_hours'))
            },
            jours: {
                min: @json(__('home.book_delivery_min_label')),
                max: @json(__('home.book_delivery_max_label')),
                hint: @json(__('home.book_delivery_hint'))
            }
        };

        var unite = document.getElementById('delaiUniteInput');
        var min = document.getElementById('delaiMinInput');
        var max = document.getElementById('delaiMaxInput');
        var minLabel = document.getElementById('delaiMinLabel');
        var maxLabel = document.getElementById('delaiMaxLabel');
        var hint = document.getElementById('delaiHint');
        if (!unite || !min || !max) return;

        function apply(){
            var key = unite.value === 'heures' ? 'heures' : 'jours';
            var limit = LIMITS[key];

            [min, max].forEach(function(input){
                input.max = limit;
                if (parseInt(input.value, 10) > limit) input.value = limit;
            });

            if (minLabel) minLabel.textContent = TEXTS[key].min;
            if (maxLabel) maxLabel.textContent = TEXTS[key].max;
            if (hint) hint.textContent = TEXTS[key].hint;
        }

        unite.addEventListener('change', apply);
        apply();
    })();
</script>