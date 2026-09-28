@extends('layouts.admin')

@section('admin_title', 'Paramètres')

@php
    $tabActif = in_array(request('tab'), ['commission', 'paiement', 'newsletter'], true)
        ? request('tab')
        : 'commission';

    // Rouvre automatiquement le bon onglet si une erreur de validation
    // concerne ses champs (sinon l'erreur resterait invisible).
    $commissionHasError = $errors->hasAny(['tier1_max', 'tier2_max', 'tier1_rate', 'tier2_rate', 'tier3_rate']);
    $newsletterHasError = $errors->hasAny(['newsletter_subject', 'newsletter_message']);
    if ($commissionHasError) { $tabActif = 'commission'; }
    if ($newsletterHasError) { $tabActif = 'newsletter'; }
@endphp

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="admin-settings-flash" style="background: rgba(179,38,30,.09); border-color: rgba(179,38,30,.3); color:#b3261e;">
            {{ session('error') }}
        </div>
    @endif

    {{-- ============ ONGLETS ============ --}}
    <div class="admin-page-tabs" role="tablist">
        <button type="button" class="admin-page-tab {{ $tabActif === 'commission' ? 'active' : '' }}"
                role="tab" aria-selected="{{ $tabActif === 'commission' ? 'true' : 'false' }}"
                data-admin-tab="commission">
            Commission
        </button>
        <button type="button" class="admin-page-tab {{ $tabActif === 'paiement' ? 'active' : '' }}"
                role="tab" aria-selected="{{ $tabActif === 'paiement' ? 'true' : 'false' }}"
                data-admin-tab="paiement">
            Paiements mobile money
        </button>
        <button type="button" class="admin-page-tab {{ $tabActif === 'newsletter' ? 'active' : '' }}"
                role="tab" aria-selected="{{ $tabActif === 'newsletter' ? 'true' : 'false' }}"
                data-admin-tab="newsletter">
            Newsletter
            <span class="admin-page-tab-count">{{ $newsletterTotal }}</span>
        </button>
    </div>

    {{-- ============ ONGLET 1 : BARÈME DE COMMISSION ============ --}}
    <div class="admin-page-panel {{ $tabActif === 'commission' ? 'active' : '' }}" id="tab-commission" role="tabpanel">
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
    </div>

    {{-- ============ ONGLET 2 : NUMÉROS DE PAIEMENT (mobile money) ============ --}}
    <div class="admin-page-panel {{ $tabActif === 'paiement' ? 'active' : '' }}" id="tab-paiement" role="tabpanel">
        <div class="admin-card" style="max-width: 560px;">
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
    </div>

    {{-- ============ ONGLET 3 : NEWSLETTER ============ --}}
    <div class="admin-page-panel {{ $tabActif === 'newsletter' ? 'active' : '' }}" id="tab-newsletter" role="tabpanel">

        {{-- ---- envoi en un clic ---- --}}
        <div class="admin-card" style="max-width: 720px; margin-bottom:24px;">
            <h3 class="admin-card-title" style="margin-bottom:6px;">Envoyer un email</h3>
            <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 20px;">
                Envoyé en une fois à tous les {{ $newsletterTotal }} abonné(s) (en copie cachée, personne ne voit les autres destinataires).
            </p>

            <form method="POST" action="{{ route('admin.parametres.newsletter.send') }}" onsubmit="return confirm('Envoyer cet email à {{ $newsletterTotal }} abonné(s) ?');">
                @csrf
                <label style="display:block; margin-bottom:12px;">
                    <span class="admin-form-label">Sujet</span>
                    <input type="text" name="newsletter_subject" value="{{ old('newsletter_subject') }}" required class="admin-input" style="width:100%;">
                </label>
                @error('newsletter_subject')<p class="admin-tier-error">{{ $message }}</p>@enderror

                <label style="display:block; margin-bottom:14px;">
                    <span class="admin-form-label">Message</span>
                    <textarea name="newsletter_message" rows="6" required class="admin-input" style="width:100%; font-family:inherit; resize:vertical;">{{ old('newsletter_message') }}</textarea>
                </label>
                @error('newsletter_message')<p class="admin-tier-error">{{ $message }}</p>@enderror

                <button type="submit" class="admin-btn" @disabled($newsletterTotal === 0)>
                    Envoyer à {{ $newsletterTotal }} abonné(s)
                </button>
            </form>
        </div>

        {{-- ---- liste des abonnés ---- --}}
        <div class="admin-card" style="max-width: 720px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:6px;">
                <h3 class="admin-card-title" style="margin-bottom:0;">Abonnés</h3>
                <a href="{{ route('admin.parametres.newsletter.export') }}" class="admin-btn admin-btn-ghost">Exporter en CSV</a>
            </div>
            <p style="color:#8a7a6d; font-size:.88rem; margin:0 0 18px;">
                {{ $newsletterTotal }} abonné(s) inscrit(s) depuis le footer du site.
            </p>

            @if($newsletterSubscribers->isEmpty())
                <div class="admin-empty-state" style="padding:32px 16px;">
                    <p style="margin:0;">Aucun abonné pour l'instant.</p>
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Inscrit le</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newsletterSubscribers as $subscriber)
                                <tr>
                                    <td data-label="Email" style="font-weight:600;">{{ $subscriber->email }}</td>
                                    <td data-label="Inscrit le" style="color:#96897d;">{{ $subscriber->created_at->format('d/m/Y H:i') }}</td>
                                    <td data-label="">
                                        <form method="POST" action="{{ route('admin.parametres.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Retirer cet abonné ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger">Retirer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($newsletterSubscribers->hasPages())
                    <div class="admin-pager">
                        @if($newsletterSubscribers->onFirstPage())
                            <span class="is-off">&larr; Précédent</span>
                        @else
                            <a href="{{ $newsletterSubscribers->previousPageUrl() }}&tab=newsletter">&larr; Précédent</a>
                        @endif
                        <span class="admin-pager-info">Page {{ $newsletterSubscribers->currentPage() }} / {{ $newsletterSubscribers->lastPage() }}</span>
                        @if($newsletterSubscribers->hasMorePages())
                            <a href="{{ $newsletterSubscribers->nextPageUrl() }}&tab=newsletter">Suivant &rarr;</a>
                        @else
                            <span class="is-off">Suivant &rarr;</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>

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

    /* ---------- onglets de la page Paramètres ---------- */
    .admin-page-tabs{
        display: flex;
        gap: 6px;
        margin-bottom: 22px;
        border-bottom: 1px solid rgba(85,16,29,.1);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .admin-page-tab{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        border: 0;
        background: none;
        font-family: inherit;
        font-size: .9rem;
        font-weight: 600;
        color: #8a7a6d;
        padding: 11px 16px;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
        cursor: pointer;
        white-space: nowrap;
        transition: color .15s ease, border-color .15s ease;
    }
    .admin-page-tab:hover{ color: var(--maroon-800); }
    .admin-page-tab.active{ color: var(--maroon-900); border-bottom-color: var(--gold); }
    .admin-page-tab-count{
        font-size: .7rem;
        font-weight: 700;
        background: rgba(85,16,29,.08);
        color: var(--maroon-800);
        padding: 2px 8px;
        border-radius: 999px;
    }
    .admin-page-tab.active .admin-page-tab-count{ background: rgba(233,178,63,.28); }

    .admin-page-panel{ display: none; }
    .admin-page-panel.active{ display: block; animation: adminPanelFade .18s ease; }
    @keyframes adminPanelFade{ from{ opacity: 0; transform: translateY(4px); } to{ opacity: 1; transform: none; } }

    @media (max-width: 480px){
        .admin-page-tab{ padding: 10px 12px; font-size: .84rem; }
    }
</style>
@endpush

@push('admin_scripts')
<script>
    (function(){
        var tabs = document.querySelectorAll('[data-admin-tab]');

        tabs.forEach(function(tab){
            tab.addEventListener('click', function(){
                var cible = tab.getAttribute('data-admin-tab');

                tabs.forEach(function(autre){
                    var actif = autre === tab;
                    autre.classList.toggle('active', actif);
                    autre.setAttribute('aria-selected', actif ? 'true' : 'false');
                });

                document.querySelectorAll('.admin-page-panel').forEach(function(panel){
                    panel.classList.toggle('active', panel.id === 'tab-' + cible);
                });

                var url = new URL(window.location.href);
                url.searchParams.set('tab', cible);
                window.history.replaceState({}, '', url);
            });
        });
    })();
</script>
@endpush