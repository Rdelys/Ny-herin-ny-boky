@extends('layouts.admin')

@section('admin_title', 'Dashboard')

@section('admin_content')

    {{-- ============ CHIFFRES RÉELS ============ --}}
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
        <div class="admin-stat-card admin-stat-card-highlight">
            <span class="admin-stat-label">Valeur potentielle du catalogue</span>
            <strong class="admin-stat-value">{{ number_format($catalogPotentialValue, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">Prix vendeur, hors commission</span>
        </div>
    </div>

    {{-- ============ APERÇUS STATIQUES ============ --}}
    {{-- Nécessitent un suivi des connexions / vues / commandes / paiements
         pas encore construit. Chiffres d'exemple, non réels. --}}
    <div class="admin-preview-banner">
        <span>Aperçu</span> Les cartes et graphiques ci-dessous utilisent des chiffres d'exemple : le suivi des connexions, des vues et des paiements n'existe pas encore côté base de données.
    </div>

    <div class="admin-stat-grid">
        <div class="admin-stat-card admin-stat-card-preview">
            <span class="admin-preview-badge">Aperçu</span>
            <span class="admin-stat-label">Vues du site</span>
            <strong class="admin-stat-value">{{ number_format($previewSiteViews, 0, ',', ' ') }}</strong>
            <span class="admin-stat-sub">7 derniers jours</span>
        </div>
        <div class="admin-stat-card admin-stat-card-preview">
            <span class="admin-preview-badge">Aperçu</span>
            <span class="admin-stat-label">Argent en attente</span>
            <strong class="admin-stat-value">{{ number_format($previewPendingPayment, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $previewOrdersPending }} commande(s)</span>
        </div>
        <div class="admin-stat-card admin-stat-card-preview">
            <span class="admin-preview-badge">Aperçu</span>
            <span class="admin-stat-label">Argent payé</span>
            <strong class="admin-stat-value">{{ number_format($previewPaidOut, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $previewOrdersPaid }} commande(s)</span>
        </div>
        <div class="admin-stat-card admin-stat-card-preview">
            <span class="admin-preview-badge">Aperçu</span>
            <span class="admin-stat-label">Bénéfice (commission 10%)</span>
            <strong class="admin-stat-value">{{ number_format($previewProfit, 0, ',', ' ') }} Ar</strong>
        </div>
    </div>

    {{-- ============ GRAPHIQUES (aperçu) ============ --}}
    <div class="admin-charts-grid">
        <div class="admin-card">
            <h3 class="admin-card-title">Vues du site — 7 derniers jours <span class="admin-preview-badge" style="position:static;">Aperçu</span></h3>
            <canvas id="viewsChart" height="180"></canvas>
        </div>
        <div class="admin-card">
            <h3 class="admin-card-title">Paiements : reçus vs en attente <span class="admin-preview-badge" style="position:static;">Aperçu</span></h3>
            <canvas id="paymentsChart" height="180"></canvas>
        </div>
        <div class="admin-card" style="grid-column: 1 / -1;">
            <h3 class="admin-card-title">Connexions par utilisateur <span class="admin-preview-badge" style="position:static;">Aperçu</span></h3>
            <canvas id="loginsChart" height="140"></canvas>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <script>
        (function(){
            var goldColor = '#e9b23f';
            var maroonColor = '#6c1524';
            var greenColor = '#5c8a37';

            new Chart(document.getElementById('viewsChart'), {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Vues',
                        data: @json($previewWeeklyViews),
                        borderColor: maroonColor,
                        backgroundColor: 'rgba(108,21,36,.08)',
                        tension: .35,
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            new Chart(document.getElementById('paymentsChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Payé', 'En attente'],
                    datasets: [{
                        data: [{{ $previewPaidOut }}, {{ $previewPendingPayment }}],
                        backgroundColor: [greenColor, goldColor]
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(document.getElementById('loginsChart'), {
                type: 'bar',
                data: {
                    labels: @json(collect($previewLoginsPerUser)->pluck('name')),
                    datasets: [{
                        label: 'Connexions',
                        data: @json(collect($previewLoginsPerUser)->pluck('count')),
                        backgroundColor: goldColor,
                        borderRadius: 6
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        })();
    </script>

@endsection