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

    {{-- ============ NUMÉROS DE PAIEMENT (mobile money) ============ --}}
    <div class="admin-card" style="max-width: 560px; margin-top: 24px;">
        <h3 class="admin-card-title" style="margin-bottom:6px;">Numéros de paiement</h3>
        <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 22px;">
            Numéro ET nom du titulaire de la puce, pour chaque opérateur. Le client voit
            ces deux informations dans la modal de commande avant de payer, pour être sûr
            d'envoyer au bon compte. Laissez le numéro vide pour masquer un opérateur.
        </p>

        <form method="POST" action="{{ route('admin.parametres.paiement') }}">
            @csrf
            @foreach($paymentAccounts as $key => $account)
                <fieldset style="border:1px solid rgba(85,16,29,.14); border-radius:14px; padding:16px; margin-bottom:14px;">
                    <legend style="padding:0 8px; font-size:.82rem; font-weight:700; color:var(--maroon-800);">{{ $account['label'] }}</legend>
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                        <label>
                            <span class="admin-form-label">Numéro</span>
                            <input type="text" name="{{ $key }}_number" value="{{ old($key . '_number', $account['numero']) }}" placeholder="Ex: 034 41 266 44"
                                style="width:100%; font-family:inherit; font-size:.9rem; padding:10px 12px; border-radius:10px; border:1px solid rgba(85,16,29,.18); background:#fffdf9;">
                        </label>
                        <label>
                            <span class="admin-form-label">Nom du titulaire</span>
                            <input type="text" name="{{ $key }}_name" value="{{ old($key . '_name', $account['nom']) }}" placeholder="Nom affiché par l'opérateur"
                                style="width:100%; font-family:inherit; font-size:.9rem; padding:10px 12px; border-radius:10px; border:1px solid rgba(85,16,29,.18); background:#fffdf9;">
                        </label>
                    </div>
                    @error($key . '_number')<p style="color:#b3261e; font-size:.8rem; margin:8px 0 0;">{{ $message }}</p>@enderror
                    @error($key . '_name')<p style="color:#b3261e; font-size:.8rem; margin:8px 0 0;">{{ $message }}</p>@enderror
                </fieldset>
            @endforeach

            <button type="submit" class="admin-rate-custom-btn">Enregistrer les numéros</button>
        </form>
    </div>

@endsection