@extends('layouts.admin')

@section('admin_title', 'Paramètres')

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif

    {{-- ============ BARÈME DE COMMISSION (éditable) ============ --}}
    <div class="admin-card" style="max-width: 620px;">
        <h3 class="admin-card-title" style="margin-bottom:6px;">Barème de commission</h3>
        <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 22px;">
            Le taux de commission dépend automatiquement du prix fixé par le vendeur
            (prix d'achat ET prix de location, séparément). Modifiez les seuils et les
            taux ci-dessous — le changement s'applique immédiatement, sur tous les livres.
        </p>

        <form method="POST" action="{{ route('admin.parametres.update') }}">
            @csrf

            <div class="admin-tier-row">
                <span class="admin-tier-badge">Palier 1</span>
                <span class="admin-tier-text">Commission de</span>
                <input type="number" name="tier1_rate" min="0" max="100" step="0.1"
                    value="{{ old('tier1_rate', $tiers[0]['rate']) }}" class="admin-tier-input admin-tier-input-rate">
                <span class="admin-tier-text">% pour un prix inférieur ou égal à</span>
                <input type="number" name="tier1_max" min="0" step="1"
                    value="{{ old('tier1_max', $tiers[0]['max']) }}" class="admin-tier-input admin-tier-input-amount">
                <span class="admin-tier-text">Ar</span>
            </div>
            @error('tier1_rate')<p class="admin-tier-error">{{ $message }}</p>@enderror
            @error('tier1_max')<p class="admin-tier-error">{{ $message }}</p>@enderror

            <div class="admin-tier-row">
                <span class="admin-tier-badge">Palier 2</span>
                <span class="admin-tier-text">Commission de</span>
                <input type="number" name="tier2_rate" min="0" max="100" step="0.1"
                    value="{{ old('tier2_rate', $tiers[1]['rate']) }}" class="admin-tier-input admin-tier-input-rate">
                <span class="admin-tier-text">% de {{ number_format($tiers[0]['max'] + 1, 0, ',', ' ') }} Ar jusqu'à</span>
                <input type="number" name="tier2_max" min="0" step="1"
                    value="{{ old('tier2_max', $tiers[1]['max']) }}" class="admin-tier-input admin-tier-input-amount">
                <span class="admin-tier-text">Ar</span>
            </div>
            @error('tier2_rate')<p class="admin-tier-error">{{ $message }}</p>@enderror
            @error('tier2_max')<p class="admin-tier-error">{{ $message }}</p>@enderror

            <div class="admin-tier-row">
                <span class="admin-tier-badge">Palier 3</span>
                <span class="admin-tier-text">Commission de</span>
                <input type="number" name="tier3_rate" min="0" max="100" step="0.1"
                    value="{{ old('tier3_rate', $tiers[2]['rate']) }}" class="admin-tier-input admin-tier-input-rate">
                <span class="admin-tier-text">% au-delà (prix supérieur au palier 2)</span>
            </div>
            @error('tier3_rate')<p class="admin-tier-error">{{ $message }}</p>@enderror

            <button type="submit" class="admin-rate-custom-btn" style="margin-top:8px;">Enregistrer le barème</button>
        </form>
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

@push('admin_styles')
<style>
    .admin-tier-row{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(85,16,29,.08);
    }
    .admin-tier-row:last-of-type{ border-bottom: 0; }
    .admin-tier-badge{
        flex-shrink: 0;
        background: rgba(233,178,63,.22);
        color: var(--maroon-800);
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        padding: 4px 10px;
        border-radius: 999px;
        margin-right: 4px;
    }
    .admin-tier-text{ font-size: .88rem; color: #6b5a4d; white-space: nowrap; }
    .admin-tier-input{
        font-family: inherit;
        font-size: .9rem;
        font-weight: 600;
        color: var(--maroon-800);
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid rgba(85,16,29,.2);
        background: #fffdf9;
        text-align: center;
    }
    .admin-tier-input:focus{ outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(233,178,63,.22); }
    .admin-tier-input-rate{ width: 64px; }
    .admin-tier-input-amount{ width: 110px; }
    .admin-tier-error{ color: #b3261e; font-size: .78rem; margin: -4px 0 10px; }

    @media (max-width: 480px){
        .admin-tier-row{ flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush