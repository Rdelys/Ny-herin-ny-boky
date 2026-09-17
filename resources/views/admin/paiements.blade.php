@extends('layouts.admin')

@section('admin_title', 'Paiements des vendeurs')

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="admin-settings-flash" style="background: rgba(179,38,30,.09); border-color: rgba(179,38,30,.3); color:#b3261e;">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    {{-- ============ CE QUE LA PLATEFORME DOIT / A ENVOYÉ ============ --}}
    <div class="admin-stat-grid">
        <div class="admin-stat-card admin-stat-card-highlight">
            <span class="admin-stat-label">Argent dû aux vendeurs</span>
            <strong class="admin-stat-value">{{ number_format($totalDu, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $compteurs->get(\App\Models\Order::PAIEMENT_DU, 0) }} commande(s) à reverser</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Argent envoyé</span>
            <strong class="admin-stat-value">{{ number_format($totalEnvoye, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $compteurs->get(\App\Models\Order::PAIEMENT_ENVOYE, 0) }} commande(s) réglée(s)</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Vendeurs à payer</span>
            <strong class="admin-stat-value">{{ $parVendeur->where('du', '>', 0)->count() }}</strong>
            <span class="admin-stat-sub">sur {{ $parVendeur->count() }} vendeur(s) ayant vendu</span>
        </div>
    </div>

    {{-- ============ RÉCAPITULATIF PAR VENDEUR ============ --}}
    <h2 class="admin-section-title">Récapitulatif par vendeur</h2>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($parVendeur->isEmpty())
            <div class="admin-empty-state">
                <h2>Aucune vente pour l'instant</h2>
                <p>Dès qu'une commande est passée, le montant à reverser au vendeur apparaît ici.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Vendeur</th>
                            <th>Numéro de paiement</th>
                            <th>Commandes</th>
                            <th>Dû</th>
                            <th>Envoyé</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parVendeur as $ligne)
                            <tr>
                                <td data-label="Vendeur">
                                    <a href="{{ route('admin.users.show', $ligne->seller) }}" style="color:var(--maroon-800); font-weight:600;">
                                        {{ $ligne->seller->sellerProfile->nom_entreprise ?? $ligne->seller->name }}
                                    </a>
                                </td>
                                <td data-label="Numéro de paiement">
                                    {{ $ligne->seller->sellerProfile->numero_paiement ?: '—' }}
                                </td>
                                <td data-label="Commandes">{{ $ligne->commandes }}</td>
                                <td data-label="Dû">
                                    <strong style="color:{{ $ligne->du > 0 ? '#b3261e' : 'inherit' }};">
                                        {{ number_format($ligne->du, 0, ',', ' ') }} Ar
                                    </strong>
                                </td>
                                <td data-label="Envoyé">{{ number_format($ligne->envoye, 0, ',', ' ') }} Ar</td>
                                <td data-label="">
                                    <a href="{{ route('admin.paiements', ['vendeur' => $ligne->seller_id, 'statut' => \App\Models\Order::PAIEMENT_DU]) }}"
                                       class="admin-btn admin-btn-ghost">Voir le détail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ============ DÉTAIL COMMANDE PAR COMMANDE ============ --}}
    <h2 class="admin-section-title">
        Détail des reversements
        @if($vendeurActif)
            — {{ $vendeurActif->sellerProfile->nom_entreprise ?? $vendeurActif->name }}
        @endif
    </h2>

    <div class="admin-filter-bar">
        <a href="{{ route('admin.paiements', ['vendeur' => $vendeurActif?->id]) }}"
           class="admin-rate-preset-btn {{ $statutActif === null ? 'active' : '' }}">
            Tous ({{ $compteurs->sum() }})
        </a>
        @foreach(\App\Models\Order::PAIEMENT_LABELS as $key => $label)
            <a href="{{ route('admin.paiements', ['statut' => $key, 'vendeur' => $vendeurActif?->id]) }}"
               class="admin-rate-preset-btn {{ $statutActif === $key ? 'active' : '' }}">
                {{ $label }} ({{ $compteurs->get($key, 0) }})
            </a>
        @endforeach

        <form method="GET" action="{{ route('admin.paiements') }}" style="display:flex; gap:8px; margin-left:auto;">
            @if($statutActif)
                <input type="hidden" name="statut" value="{{ $statutActif }}">
            @endif
            <select name="vendeur" class="admin-input" onchange="this.form.submit()">
                <option value="">Tous les vendeurs</option>
                @foreach($vendeurs as $vendeur)
                    <option value="{{ $vendeur->id }}" @selected($vendeurActif?->id === $vendeur->id)>
                        {{ $vendeur->sellerProfile->nom_entreprise ?? $vendeur->name }}
                    </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="admin-btn">Filtrer</button></noscript>
        </form>
    </div>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($orders->isEmpty())
            <div class="admin-empty-state">
                <h2>Rien à afficher</h2>
                <p>Aucune commande ne correspond à ce filtre.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Vendeur</th>
                            <th>Livre</th>
                            <th>À reverser</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php($estDu = $order->paiement_vendeur === \App\Models\Order::PAIEMENT_DU)
                            <tr>
                                <td data-label="Référence">
                                    <div>
                                        <strong>{{ $order->reference }}</strong><br>
                                        <span class="admin-table-sub">{{ $order->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td data-label="Vendeur">
                                    <a href="{{ route('admin.users.show', $order->seller) }}" style="color:var(--maroon-800); font-weight:600;">
                                        {{ $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name }}
                                    </a>
                                </td>
                                <td data-label="Livre">
                                    <div>
                                        {{ $order->book_titre }}<br>
                                        <span class="admin-table-sub">Qté : {{ $order->quantite }}</span>
                                    </div>
                                </td>
                                <td data-label="À reverser">
                                    <div>
                                        <strong>{{ number_format($order->montant_vendeur, 0, ',', ' ') }} Ar</strong><br>
                                        <span class="admin-table-sub">Commission : {{ number_format($order->commission, 0, ',', ' ') }} Ar</span>
                                    </div>
                                </td>
                                <td data-label="Statut">
                                    <div>
                                        <span class="admin-badge admin-badge-{{ $order->paiement_vendeur }}">{{ $order->paiement_vendeur_label }}</span>
                                        @if($order->paiement_vendeur_at)
                                            <br><span class="admin-table-sub">{{ $order->paiement_vendeur_at->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Action">
                                    <form method="POST" action="{{ route('admin.paiements.reversement', $order) }}">
                                        @csrf
                                        <input type="hidden" name="paiement_vendeur"
                                               value="{{ $estDu ? \App\Models\Order::PAIEMENT_ENVOYE : \App\Models\Order::PAIEMENT_DU }}">
                                        <button type="submit" class="admin-btn {{ $estDu ? 'admin-btn-green' : 'admin-btn-ghost' }}">
                                            {{ $estDu ? 'Marquer envoyé' : 'Remettre en dû' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($orders->hasPages())
        <div class="admin-pager">
            @if($orders->onFirstPage())
                <span class="is-off">&larr; Précédent</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}">&larr; Précédent</a>
            @endif
            <span class="admin-pager-info">Page {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}">Suivant &rarr;</a>
            @else
                <span class="is-off">Suivant &rarr;</span>
            @endif
        </div>
    @endif
@endsection
