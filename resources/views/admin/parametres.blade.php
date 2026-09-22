@extends('layouts.admin')

@section('admin_title', 'Paramètres')

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif

    <div class="admin-card" style="max-width: 560px;">
        <h3 class="admin-card-title" style="margin-bottom:6px;">Barème de commission</h3>
        <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 22px;">
            Le taux appliqué dépend automatiquement du prix fixé par le vendeur
            pour chaque livre. Ce barème est une règle métier fixée dans le code
            (app/Models/Setting.php) : il ne se modifie pas depuis cette page,
            pour éviter qu'une erreur de saisie ne fausse tous les prix du site.
        </p>
 
        <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
            <thead>
                <tr style="background: rgba(85,16,29,.03);">
                    <th style="text-align:left; padding:12px 14px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Prix du livre</th>
                    <th style="text-align:left; padding:12px 14px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Commission</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-top:1px solid rgba(85,16,29,.06);">
                    <td style="padding:10px 14px;">Moins de 60 000 Ar</td>
                    <td style="padding:10px 14px; font-weight:700; color:var(--maroon-800);">10%</td>
                </tr>
                <tr style="border-top:1px solid rgba(85,16,29,.06);">
                    <td style="padding:10px 14px;">De 60 000 à 99 999 Ar</td>
                    <td style="padding:10px 14px; font-weight:700; color:var(--maroon-800);">8%</td>
                </tr>
                <tr style="border-top:1px solid rgba(85,16,29,.06);">
                    <td style="padding:10px 14px;">100 000 Ar et plus</td>
                    <td style="padding:10px 14px; font-weight:700; color:var(--maroon-800);">5%</td>
                </tr>
            </tbody>
        </table>
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