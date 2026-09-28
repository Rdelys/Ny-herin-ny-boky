@extends('layouts.admin')

@section('admin_title', 'Dashboard')

@section('admin_content')

    {{-- ============ ARGENT ============ --}}
    <div class="admin-stat-grid">
        <div class="admin-stat-card admin-stat-card-highlight">
            <span class="admin-stat-label">Encaissé</span>
            <strong class="admin-stat-value">{{ number_format($encaisse, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $totalOrders }} commande(s) au total</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Bénéfice (commission selon barème)</span>
            <strong class="admin-stat-value">{{ number_format($benefice, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">Part gardée par la plateforme</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Argent dû aux vendeurs</span>
            <strong class="admin-stat-value" style="color:{{ $argentDu > 0 ? '#b3261e' : 'inherit' }};">
                {{ number_format($argentDu, 0, ',', ' ') }} Ar
            </strong>
            <span class="admin-stat-sub">
                {{ $compteursPaiement->get(\App\Models\Order::PAIEMENT_DU, 0) }} commande(s) —
                <a href="{{ route('admin.paiements', ['statut' => \App\Models\Order::PAIEMENT_DU]) }}" style="color:var(--maroon-800); font-weight:600;">reverser</a>
            </span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Argent envoyé</span>
            <strong class="admin-stat-value">{{ number_format($argentEnvoye, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $compteursPaiement->get(\App\Models\Order::PAIEMENT_ENVOYE, 0) }} commande(s) réglée(s)</span>
        </div>
    </div>

    {{-- ============ COMMANDES PAR STATUT ============ --}}
    <div class="admin-stat-grid">
        @foreach(\App\Models\Order::STATUT_LABELS as $key => $label)
            <div class="admin-stat-card">
                <span class="admin-stat-label">{{ $label }}</span>
                <strong class="admin-stat-value">{{ $compteursStatut->get($key, 0) }}</strong>
                <span class="admin-stat-sub">
                    <a href="{{ route('admin.commandes', ['statut' => $key]) }}" style="color:var(--maroon-800); font-weight:600;">Voir les commandes</a>
                </span>
            </div>
        @endforeach
    </div>

    {{-- ============ UTILISATEURS & CATALOGUE ============ --}}
    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <span class="admin-stat-label">Utilisateurs</span>
            <strong class="admin-stat-value">{{ $totalUsers }}</strong>
            <span class="admin-stat-sub">{{ $totalClients }} clients · {{ $totalSellers }} vendeurs</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Livres publiés</span>
            <strong class="admin-stat-value">{{ $totalBooks }}</strong>
            <span class="admin-stat-sub">{{ $totalStock }} exemplaires en stock</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-label">Valeur potentielle du catalogue</span>
            <strong class="admin-stat-value">{{ number_format($catalogPotentialValue, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">Prix vendeur, hors commission</span>
        </div>
    </div>

    {{-- ============ GRAPHIQUES (données réelles) ============ --}}
    <div class="admin-charts-grid">
        <div class="admin-card">
            <h3 class="admin-card-title">Commandes — 7 derniers jours</h3>
            <canvas id="ordersChart" height="180"></canvas>
        </div>
        <div class="admin-card">
            <h3 class="admin-card-title">Reversements vendeurs</h3>
            <canvas id="payoutsChart" height="180"></canvas>
        </div>
        <div class="admin-card" style="grid-column: 1 / -1;">
            <h3 class="admin-card-title">Top vendeurs — chiffre d'affaires</h3>
            @if($topVendeurs->isEmpty())
                <p style="color:#9c8b7d; font-size:.88rem; margin:0;">Aucune vente enregistrée pour l'instant.</p>
            @else
                <canvas id="sellersChart" height="140"></canvas>
            @endif
        </div>
    </div>

    {{-- Chart.js est servi depuis /public/js : le CDN utilisé avant
         renvoyait une 404, et les graphiques restaient donc vides. --}}
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>
        (function(){
            if (typeof Chart === 'undefined') {
                document.querySelectorAll('.admin-charts-grid canvas').forEach(function(canvas){
                    var message = document.createElement('p');
                    message.style.cssText = 'color:#b3261e; font-size:.85rem; margin:0;';
                    message.textContent = 'Les graphiques n’ont pas pu être chargés (chart.umd.min.js introuvable).';
                    canvas.replaceWith(message);
                });
                return;
            }

            var gold = '#e9b23f';
            var maroon = '#6c1524';
            var green = '#5c8a37';
            var red = '#b3261e';

            Chart.defaults.font.family = "'Inter', sans-serif";

            new Chart(document.getElementById('ordersChart'), {
                type: 'line',
                data: {
                    labels: @json($joursLabels),
                    datasets: [
                        {
                            label: 'Commandes',
                            data: @json($joursCommandes),
                            borderColor: maroon,
                            backgroundColor: 'rgba(108,21,36,.08)',
                            tension: .35,
                            fill: true,
                            pointRadius: 3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Encaissé (Ar)',
                            data: @json($joursRevenus),
                            borderColor: gold,
                            backgroundColor: 'rgba(233,178,63,.12)',
                            tension: .35,
                            fill: true,
                            pointRadius: 3,
                            yAxisID: 'yAr'
                        }
                    ]
                },
                options: {
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Commandes' } },
                        yAr: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Ar' } }
                    }
                }
            });

            new Chart(document.getElementById('payoutsChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Envoyé', 'Dû'],
                    datasets: [{
                        data: [{{ $argentEnvoye }}, {{ $argentDu }}],
                        backgroundColor: [green, red]
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            var sellersCanvas = document.getElementById('sellersChart');
            if (sellersCanvas) {
                new Chart(sellersCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($topVendeurs->map(fn ($l) => $l->seller->sellerProfile->nom_entreprise ?? $l->seller->name)->values()),
                        datasets: [{
                            label: "Chiffre d'affaires (Ar)",
                            data: @json($topVendeurs->pluck('ca')->map(fn ($v) => (int) $v)->values()),
                            backgroundColor: gold,
                            borderRadius: 6
                        }]
                    },
                    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }
        })();
    </script>

@endsection
