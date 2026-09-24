@extends('layouts.admin')

@section('admin_title', 'Commandes')

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

    {{-- ---- filtres par statut de livraison ---- --}}
    <div class="admin-rate-presets" style="margin-bottom:22px;">
        <a href="{{ route('admin.commandes') }}" class="admin-rate-preset-btn {{ $statutActif === null ? 'active' : '' }}">
            Toutes ({{ $compteurs->sum() }})
        </a>
        @foreach(\App\Models\Order::STATUT_LABELS as $key => $label)
            <a href="{{ route('admin.commandes', ['statut' => $key]) }}" class="admin-rate-preset-btn {{ $statutActif === $key ? 'active' : '' }}">
                {{ $label }} ({{ $compteurs->get($key, 0) }})
            </a>
        @endforeach
    </div>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($orders->isEmpty())
            <div class="admin-empty-state">
                <h2>Aucune commande</h2>
                <p>Les commandes passées par les clients apparaîtront ici, avec leur référence de paiement à vérifier.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Livre</th>
                            <th>Acheteur</th>
                            <th>Vendeur</th>
                            <th>Paiement client</th>
                            <th>Montant</th>
                            <th>Statut / Livreur</th>
                            <th>Facture</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td data-label="Référence">
                                    <div>
                                        <strong>{{ $order->reference }}</strong><br>
                                        <span class="admin-table-sub">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </td>
                                <td data-label="Livre">
                                    <div>
                                        {{ $order->book_titre }}<br>
                                        <span class="admin-table-sub">Qté : {{ $order->quantite }}</span>
                                    </div>
                                </td>
                                <td data-label="Acheteur">
                                    <div>
                                        {{ $order->buyer_name }}<br>
                                        <span class="admin-table-sub">{{ $order->buyer_contact }}</span>
                                        @if($order->isGuestOrder())
                                            <br><span class="admin-badge" style="background:rgba(233,178,63,.2); color:#8a5f14;">Invité</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Vendeur">
                                    <a href="{{ route('admin.users.show', $order->seller) }}" style="color:var(--maroon-800); font-weight:600;">
                                        {{ $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name }}
                                    </a>
                                </td>
                                <td data-label="Paiement client">
                                    <div>
                                        <strong>{{ $order->mode_paiement_label }}</strong><br>
                                        <span class="admin-table-sub">Réf : {{ $order->reference_paiement }}</span>
                                    </div>
                                </td>
                                <td data-label="Montant">
                                    <div>
                                        <strong>{{ number_format($order->total, 0, ',', ' ') }} Ar</strong><br>
                                        <span class="admin-table-sub">Vendeur : {{ number_format($order->montant_vendeur, 0, ',', ' ') }} Ar</span>
                                    </div>
                                </td>
                                <td data-label="Statut / Livreur" style="min-width:220px;">
                                    <form method="POST" action="{{ route('admin.commandes.statut', $order) }}" class="order-status-form">
                                        @csrf
                                        <select name="statut" class="admin-input" data-order-status-select>
                                            @foreach(\App\Models\Order::STATUT_LABELS as $key => $label)
                                                <option value="{{ $key }}" @selected($order->statut === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>

                                        <select name="deliverer_id" class="admin-input" data-order-deliverer-select style="margin-top:8px; {{ $order->statut === 'en_livraison' ? '' : 'display:none;' }}">
                                            <option value="">— Choisir un livreur —</option>
                                            @foreach($livreurs as $livreur)
                                                <option value="{{ $livreur->id }}" @selected($order->deliverer_id === $livreur->id)>{{ $livreur->nom }} ({{ $livreur->telephone }})</option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="admin-btn" style="margin-top:8px;">Mettre à jour</button>
                                    </form>
                                </td>
                                <td data-label="Facture">
                                    @if($order->facture_url)
                                        <a href="{{ $order->facture_url }}" target="_blank" class="admin-btn admin-btn-ghost">Voir</a>
                                    @endif
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

    @push('admin_styles')
        <style>
            .order-status-form{ display:flex; flex-direction:column; align-items:stretch; width:100%; }
            @media (max-width: 720px){
                .admin-table td[data-label="Statut / Livreur"]{ flex-direction: column; align-items: stretch; text-align: left; }
            }
        </style>
    @endpush

    @push('admin_scripts')
        <script>
            // Le choix du livreur n'a de sens que pour le statut "en_livraison" :
            // on ne l'affiche que dans ce cas, pour ne pas induire l'admin en erreur.
            document.querySelectorAll('[data-order-status-select]').forEach(function (select) {
                var delivererSelect = select.closest('form').querySelector('[data-order-deliverer-select]');
                if (!delivererSelect) return;
                select.addEventListener('change', function () {
                    delivererSelect.style.display = (select.value === 'en_livraison') ? '' : 'none';
                });
            });
        </script>
    @endpush
@endsection
