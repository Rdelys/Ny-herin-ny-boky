@extends('layouts.admin')

@section('admin_title', 'Paramètres')

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif

    <div class="admin-card" style="max-width: 560px;">
        <h3 class="admin-card-title" style="margin-bottom:6px;">Taux de commission</h3>
        <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 22px;">
            Ce taux est ajouté au prix fixé par chaque vendeur pour obtenir le prix affiché
            aux acheteurs, partout sur le site (accueil, catalogue, fiche vendeur, modal de commande).
            Le changement est appliqué immédiatement, sur tous les livres.
        </p>

        <div class="admin-current-rate">
            <span>Taux actuel</span>
            <strong>{{ rtrim(rtrim(number_format($commissionRate, 2, ',', ' '), '0'), ',') }}%</strong>
        </div>

        {{-- Formulaire 1 : boutons "choix rapide" — dans SON PROPRE <form>,
             pour ne pas partager le name="commission_rate" avec le champ
             personnalisé ci-dessous (c'était la cause du bug : quand les
             deux étaient dans le même <form>, la valeur de l'input
             personnalisé écrasait systématiquement celle du bouton cliqué). --}}
        <form method="POST" action="{{ route('admin.parametres.update') }}">
            @csrf
            <label class="admin-form-label">Choix rapide</label>
            <div class="admin-rate-presets">
                @foreach(\App\Http\Controllers\Admin\AdminSettingsController::QUICK_RATES as $rate)
                    <button type="submit" name="commission_rate" value="{{ $rate }}"
                        class="admin-rate-preset-btn {{ (float) $commissionRate === (float) $rate ? 'active' : '' }}">
                        {{ $rate }}%
                    </button>
                @endforeach
            </div>
        </form>

        {{-- Formulaire 2 : taux personnalisé — complètement indépendant --}}
        <form method="POST" action="{{ route('admin.parametres.update') }}" style="margin-top:20px;">
            @csrf
            <label class="admin-form-label" for="commission_rate_custom">Ou taux personnalisé</label>
            <div class="admin-rate-custom-row">
                <input type="number" id="commission_rate_custom" name="commission_rate" min="0" max="100" step="0.1"
                    value="{{ old('commission_rate', $commissionRate) }}" placeholder="Ex: 7.5">
                <span>%</span>
                <button type="submit" class="admin-rate-custom-btn">Enregistrer</button>
            </div>
        </form>

        @error('commission_rate')
            <p style="color:#b3261e; font-size:.82rem; margin:12px 0 0;">{{ $message }}</p>
        @enderror
    </div>

@endsection