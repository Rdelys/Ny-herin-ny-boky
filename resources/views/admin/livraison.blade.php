@extends('layouts.admin')

@section('admin_title', 'Livraison')

@section('admin_content')

    <style>
        .liv-tabs {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-bottom: 1px solid rgba(128, 128, 128, .28);
            margin-bottom: 22px;
            padding-bottom: 0;
            scrollbar-width: thin;
        }
        .liv-tab {
            appearance: none;
            background: transparent;
            border: 0;
            border-bottom: 3px solid transparent;
            padding: 11px 16px;
            font: inherit;
            font-weight: 600;
            color: inherit;
            opacity: .65;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: -1px;
            transition: opacity .15s, border-color .15s;
        }
        .liv-tab:hover { opacity: .9; }
        .liv-tab:focus-visible { outline: 2px solid currentColor; outline-offset: -2px; border-radius: 6px; }
        .liv-tab.is-active {
            opacity: 1;
            border-bottom-color: currentColor;
        }
        .liv-tab-count {
            display: inline-block;
            min-width: 22px;
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
            text-align: center;
            background: rgba(128, 128, 128, .18);
        }
        .liv-tab-count.is-alert {
            background: #b3261e;
            color: #fff;
        }
        .liv-panel { display: none; }
        .liv-panel.is-active { display: block; }
        @media (max-width: 640px) {
            .liv-tab { padding: 10px 12px; font-size: 14px; }
        }
    </style>

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="admin-settings-flash" style="background: rgba(179,38,30,.09); border-color: rgba(179,38,30,.3); color:#b3261e;">
            @foreach($errors->all() as $error){{ $error }}<br>@endforeach
        </div>
    @endif

    @php $nbAValider = $pendingQuartiers->count() + $pendingCoops->count(); @endphp

    {{-- ============ BARRE D'ONGLETS ============ --}}
    <div class="liv-tabs" role="tablist" aria-label="Sections livraison">
        @if($nbAValider)
            <button type="button" class="liv-tab" role="tab" id="tab-valider" data-tab="valider" aria-controls="panel-valider" aria-selected="false">
                À valider <span class="liv-tab-count is-alert">{{ $nbAValider }}</span>
            </button>
        @endif
        <button type="button" class="liv-tab" role="tab" id="tab-regles" data-tab="regles" aria-controls="panel-regles" aria-selected="false">
            Règles &amp; VIP
        </button>
        <button type="button" class="liv-tab" role="tab" id="tab-provinces" data-tab="provinces" aria-controls="panel-provinces" aria-selected="false">
            Provinces <span class="liv-tab-count">{{ $zones->count() }}</span>
        </button>
        <button type="button" class="liv-tab" role="tab" id="tab-quartiers" data-tab="quartiers" aria-controls="panel-quartiers" aria-selected="false">
            Quartiers <span class="liv-tab-count">{{ $quartiers->count() }}</span>
        </button>
        <button type="button" class="liv-tab" role="tab" id="tab-cooperatives" data-tab="cooperatives" aria-controls="panel-cooperatives" aria-selected="false">
            Coopératives <span class="liv-tab-count">{{ $cooperatives->count() }}</span>
        </button>
    </div>

    {{-- ============ ONGLET : À VALIDER ============ --}}
    @if($nbAValider)
        <div class="liv-panel" id="panel-valider" role="tabpanel" aria-labelledby="tab-valider" data-panel="valider">
            <h2 class="admin-section-title">À valider ({{ $nbAValider }})</h2>
            <div class="admin-card" style="padding:0; overflow:hidden; margin-bottom:26px;">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Panier</th><th>Saisi par le client</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingQuartiers as $d)
                                <tr>
                                    <td data-label="Panier">{{ $d->groupe_reference }}<br><span class="admin-table-sub">{{ $d->created_at->format('d/m/Y H:i') }}</span></td>
                                    <td data-label="Saisi"><strong>Quartier :</strong> {{ $d->quartier_nom }} <span class="admin-table-sub">({{ $d->zone_nom }})</span></td>
                                    <td data-label="Action">
                                        <form method="POST" action="{{ route('admin.livraison.approve.quartier', $d) }}" class="admin-row-actions">
                                            @csrf
                                            <input type="number" name="frais" min="0" required placeholder="Frais (Ar)" class="admin-input" style="width:130px;">
                                            <button type="submit" class="admin-btn">Ajouter à la liste</button>
                                        </form>
                                        @if($d->frais_a_confirmer)
                                            <form method="POST" action="{{ route('admin.livraison.frais', $d) }}" class="admin-row-actions" style="margin-top:8px;">
                                                @csrf
                                                <input type="number" name="frais" min="0" required placeholder="Frais (Ar)" class="admin-input" style="width:130px;">
                                                <button type="submit" class="admin-btn admin-btn-ghost">Fixer pour cette commande seulement</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($pendingCoops as $d)
                                <tr>
                                    <td data-label="Panier">{{ $d->groupe_reference }}<br><span class="admin-table-sub">{{ $d->created_at->format('d/m/Y H:i') }}</span></td>
                                    <td data-label="Saisi"><strong>Coopérative :</strong> {{ $d->cooperative_nom }} <span class="admin-table-sub">({{ $d->zone_nom }})</span></td>
                                    <td data-label="Action">
                                        <form method="POST" action="{{ route('admin.livraison.approve.cooperative', $d) }}" class="admin-row-actions">
                                            @csrf
                                            <input type="text" name="telephone" placeholder="Téléphone (optionnel)" class="admin-input" style="width:170px;">
                                            <button type="submit" class="admin-btn">Ajouter à la liste</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ ONGLET : RÈGLES & VIP ============ --}}
    <div class="liv-panel" id="panel-regles" role="tabpanel" aria-labelledby="tab-regles" data-panel="regles">
        <h2 class="admin-section-title">Règles de livraison &amp; tarif VIP</h2>
        <div class="admin-card" style="max-width:760px; margin-bottom:26px;">
            <form method="POST" action="{{ route('admin.livraison.reglages') }}">
                @csrf
                <label style="display:flex; align-items:center; gap:10px; font-weight:600; margin-bottom:18px;">
                    <input type="checkbox" name="vip_actif" value="1" @checked($settings['vip_actif'])>
                    Livraison VIP activée (Antananarivo uniquement)
                </label>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(190px,1fr)); gap:14px; margin-bottom:18px;">
                    <label><span class="admin-form-label">VIP : délai min (h)</span>
                        <input type="number" name="vip_min_h" min="1" value="{{ old('vip_min_h', $settings['vip_min_h']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">VIP : délai max (h)</span>
                        <input type="number" name="vip_max_h" min="2" value="{{ old('vip_max_h', $settings['vip_max_h']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">Supplément VIP (Ar)</span>
                        <input type="number" name="vip_surcharge" min="0" value="{{ old('vip_surcharge', $settings['vip_surcharge']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">VIP : début des livraisons (h)</span>
                        <input type="number" name="vip_open_hour" min="0" max="23" value="{{ old('vip_open_hour', $settings['vip_open_hour']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">VIP : fin des livraisons (h)</span>
                        <input type="number" name="vip_close_hour" min="1" max="24" value="{{ old('vip_close_hour', $settings['vip_close_hour']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">VIP coupé si délai panier &gt; (h)</span>
                        <input type="number" name="vip_max_lead_h" min="1" value="{{ old('vip_max_lead_h', $settings['vip_max_lead_h']) }}" class="admin-input" style="width:100%;" required></label>
                    <label><span class="admin-form-label">Seuil « long délai » (h)</span>
                        <input type="number" name="standard_max_h" min="1" value="{{ old('standard_max_h', $settings['standard_max_h']) }}" class="admin-input" style="width:100%;" required></label>
                </div>
                <p class="admin-table-sub" style="margin:0 0 16px;">
                    Le délai d'un panier = celui du livre le plus lent. Au-dessus du seuil VIP, le VIP disparaît ; au-dessus du
                    seuil « long délai », le client voit le délai réel du livre (ex. « 5 à 8 jours »).
                    Les livres du compte officiel du site ont les frais de base offerts (le supplément VIP reste dû).
                </p>
                <button type="submit" class="admin-btn">Enregistrer les règles</button>
            </form>
        </div>
    </div>

    {{-- ============ ONGLET : PROVINCES ============ --}}
    <div class="liv-panel" id="panel-provinces" role="tabpanel" aria-labelledby="tab-provinces" data-panel="provinces">
        <h2 class="admin-section-title">Provinces / villes desservies</h2>
        <div class="admin-card" style="padding:0; overflow:hidden; margin-bottom:14px;">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Ville</th><th>Frais livraison (Ar)</th><th>Délai min (h)</th><th>Délai max (h)</th><th>Actif</th><th>Liste</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($zones as $z)
                            <tr>
                                <td data-label="Ville" style="font-weight:600;">
                                    {{ $z->nom }}
                                    @if($z->est_capitale)<span class="admin-badge admin-badge-vendeur">Capitale</span>@endif
                                </td>
                                <td data-label="Frais">
                                    @if($z->est_capitale)
                                        <span class="admin-table-sub">selon le quartier</span>
                                    @else
                                        <input form="zf{{ $z->id }}" type="number" name="frais" min="0" value="{{ $z->frais }}" class="admin-input" style="width:120px;">
                                    @endif
                                </td>
                                <td data-label="Min"><input form="zf{{ $z->id }}" type="number" name="delai_min_h" min="1" value="{{ $z->delai_min_h }}" class="admin-input" style="width:90px;" required></td>
                                <td data-label="Max"><input form="zf{{ $z->id }}" type="number" name="delai_max_h" min="1" value="{{ $z->delai_max_h }}" class="admin-input" style="width:90px;" required></td>
                                <td data-label="Actif"><input form="zf{{ $z->id }}" type="checkbox" name="actif" value="1" @checked($z->actif) @disabled($z->est_capitale)></td>
                                <td data-label="Liste" class="admin-table-sub">{{ $z->est_capitale ? $z->quartiers_count . ' quartier(s)' : $z->cooperatives_count . ' coopérative(s)' }}</td>
                                <td data-label="">
                                    <button form="zf{{ $z->id }}" type="submit" class="admin-btn">Enregistrer</button>
                                    <form id="zf{{ $z->id }}" method="POST" action="{{ route('admin.livraison.zones.update', $z) }}">@csrf @method('PUT')</form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card" style="max-width:760px; margin-bottom:26px;">
            <h3 class="admin-card-title">Ajouter une province / ville</h3>
            <form method="POST" action="{{ route('admin.livraison.zones.store') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
                @csrf
                <label><span class="admin-form-label">Nom</span><input type="text" name="nom" class="admin-input" required></label>
                <label><span class="admin-form-label">Frais (Ar)</span><input type="number" name="frais" min="0" value="0" class="admin-input" style="width:110px;" required></label>
                <label><span class="admin-form-label">Délai min (h)</span><input type="number" name="delai_min_h" min="1" value="48" class="admin-input" style="width:100px;" required></label>
                <label><span class="admin-form-label">Délai max (h)</span><input type="number" name="delai_max_h" min="1" value="96" class="admin-input" style="width:100px;" required></label>
                <button type="submit" class="admin-btn">Ajouter</button>
            </form>
            <p class="admin-table-sub" style="margin:12px 0 0;">Dans les provinces, les frais de taxi-brousse de la coopérative sont toujours payés à l'arrivée (PA) : ils ne sont pas dans ces tarifs.</p>
        </div>
    </div>

    {{-- ============ ONGLET : QUARTIERS D'ANTANANARIVO ============ --}}
    <div class="liv-panel" id="panel-quartiers" role="tabpanel" aria-labelledby="tab-quartiers" data-panel="quartiers">
        <h2 class="admin-section-title">Quartiers d'Antananarivo</h2>
        <div class="admin-card" style="max-width:760px; margin-bottom:14px;">
            <form method="POST" action="{{ route('admin.livraison.quartiers.store') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
                @csrf
                <label><span class="admin-form-label">Quartier</span><input type="text" name="nom" class="admin-input" required></label>
                <label><span class="admin-form-label">Frais (Ar)</span><input type="number" name="frais" min="0" class="admin-input" style="width:120px;" required></label>
                <button type="submit" class="admin-btn">Ajouter</button>
            </form>
        </div>

        <div class="admin-card" style="padding:0; overflow:hidden; margin-bottom:26px;">
            @if($quartiers->isEmpty())
                <div class="admin-empty-state"><h2>Aucun quartier</h2><p>Ajoutez un quartier ci-dessus.</p></div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Quartier</th><th>Frais (Ar)</th><th>Actif</th><th></th></tr></thead>
                        <tbody>
                            @foreach($quartiers as $q)
                                <tr>
                                    <td data-label="Quartier"><input form="qf{{ $q->id }}" type="text" name="nom" value="{{ $q->nom }}" class="admin-input" required></td>
                                    <td data-label="Frais"><input form="qf{{ $q->id }}" type="number" name="frais" min="0" value="{{ $q->frais }}" class="admin-input" style="width:120px;" required></td>
                                    <td data-label="Actif"><input form="qf{{ $q->id }}" type="checkbox" name="actif" value="1" @checked($q->actif)></td>
                                    <td data-label="">
                                        <div class="admin-row-actions">
                                            <button form="qf{{ $q->id }}" type="submit" class="admin-btn">Enregistrer</button>
                                            <button form="qd{{ $q->id }}" type="submit" class="admin-btn admin-btn-danger" onclick="return confirm('Supprimer ce quartier ?');">Supprimer</button>
                                        </div>
                                        <form id="qf{{ $q->id }}" method="POST" action="{{ route('admin.livraison.quartiers.update', $q) }}">@csrf @method('PUT')</form>
                                        <form id="qd{{ $q->id }}" method="POST" action="{{ route('admin.livraison.quartiers.destroy', $q) }}">@csrf @method('DELETE')</form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ ONGLET : COOPÉRATIVES ============ --}}
    <div class="liv-panel" id="panel-cooperatives" role="tabpanel" aria-labelledby="tab-cooperatives" data-panel="cooperatives">
        <h2 class="admin-section-title">Coopératives (taxi-brousse)</h2>
        <div class="admin-card" style="max-width:760px; margin-bottom:14px;">
            <form method="POST" action="{{ route('admin.livraison.cooperatives.store') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
                @csrf
                <label><span class="admin-form-label">Province</span>
                    <select name="zone_id" class="admin-input" required>
                        @foreach($villesProvinces as $v)<option value="{{ $v->id }}">{{ $v->nom }}</option>@endforeach
                    </select>
                </label>
                <label><span class="admin-form-label">Coopérative</span><input type="text" name="nom" class="admin-input" required></label>
                <label><span class="admin-form-label">Téléphone</span><input type="text" name="telephone" class="admin-input"></label>
                <label><span class="admin-form-label">Note</span><input type="text" name="note" class="admin-input"></label>
                <button type="submit" class="admin-btn">Ajouter</button>
            </form>
        </div>

        <div class="admin-card" style="padding:0; overflow:hidden;">
            @if($cooperatives->isEmpty())
                <div class="admin-empty-state"><h2>Aucune coopérative</h2><p>Ajoutez-en une ci-dessus : elle apparaîtra dans le choix du client pour sa province.</p></div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Province</th><th>Coopérative</th><th>Téléphone</th><th>Note</th><th>Actif</th><th></th></tr></thead>
                        <tbody>
                            @foreach($cooperatives as $c)
                                <tr>
                                    <td data-label="Province" style="font-weight:600;">{{ $c->zone->nom ?? '—' }}</td>
                                    <td data-label="Coopérative"><input form="cf{{ $c->id }}" type="text" name="nom" value="{{ $c->nom }}" class="admin-input" required></td>
                                    <td data-label="Téléphone"><input form="cf{{ $c->id }}" type="text" name="telephone" value="{{ $c->telephone }}" class="admin-input" style="width:140px;"></td>
                                    <td data-label="Note"><input form="cf{{ $c->id }}" type="text" name="note" value="{{ $c->note }}" class="admin-input"></td>
                                    <td data-label="Actif"><input form="cf{{ $c->id }}" type="checkbox" name="actif" value="1" @checked($c->actif)></td>
                                    <td data-label="">
                                        <div class="admin-row-actions">
                                            <button form="cf{{ $c->id }}" type="submit" class="admin-btn">Enregistrer</button>
                                            <button form="cd{{ $c->id }}" type="submit" class="admin-btn admin-btn-danger" onclick="return confirm('Supprimer cette coopérative ?');">Supprimer</button>
                                        </div>
                                        <form id="cf{{ $c->id }}" method="POST" action="{{ route('admin.livraison.cooperatives.update', $c) }}">@csrf @method('PUT')</form>
                                        <form id="cd{{ $c->id }}" method="POST" action="{{ route('admin.livraison.cooperatives.destroy', $c) }}">@csrf @method('DELETE')</form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        (function () {
            var STORAGE_KEY = 'admin.livraison.tab';
            var tabs = Array.prototype.slice.call(document.querySelectorAll('.liv-tab'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('.liv-panel'));
            if (!tabs.length) return;

            var available = tabs.map(function (t) { return t.getAttribute('data-tab'); });
            var hasPending = available.indexOf('valider') !== -1;

            function activate(name, updateHash) {
                if (available.indexOf(name) === -1) {
                    name = hasPending ? 'valider' : 'regles';
                }
                tabs.forEach(function (t) {
                    var on = t.getAttribute('data-tab') === name;
                    t.classList.toggle('is-active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                    t.setAttribute('tabindex', on ? '0' : '-1');
                    if (on && t.scrollIntoView) {
                        try { t.scrollIntoView({ block: 'nearest', inline: 'nearest' }); } catch (e) {}
                    }
                });
                panels.forEach(function (p) {
                    p.classList.toggle('is-active', p.getAttribute('data-panel') === name);
                });
                try { localStorage.setItem(STORAGE_KEY, name); } catch (e) {}
                if (updateHash && history.replaceState) {
                    history.replaceState(null, '', '#' + name);
                }
            }

            tabs.forEach(function (t, i) {
                t.addEventListener('click', function () {
                    activate(t.getAttribute('data-tab'), true);
                });
                t.addEventListener('keydown', function (e) {
                    var next = null;
                    if (e.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
                    if (e.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
                    if (next) {
                        e.preventDefault();
                        next.focus();
                        activate(next.getAttribute('data-tab'), true);
                    }
                });
            });

            // Onglet initial : hash URL > dernier onglet utilisé > "À valider" s'il y a des demandes > Règles
            var initial = (location.hash || '').replace('#', '');
            if (available.indexOf(initial) === -1) {
                try { initial = localStorage.getItem(STORAGE_KEY) || ''; } catch (e) { initial = ''; }
            }
            // S'il y a des éléments à valider et aucun choix explicite dans l'URL, on les montre en premier
            if (hasPending && !(location.hash && available.indexOf(location.hash.replace('#', '')) !== -1)) {
                initial = initial && initial !== 'valider' ? initial : 'valider';
            }
            activate(initial, false);

            window.addEventListener('hashchange', function () {
                var h = (location.hash || '').replace('#', '');
                if (available.indexOf(h) !== -1) activate(h, false);
            });
        })();
    </script>
@endsection