<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 14mm 12mm; }
        body{ font-family: DejaVu Sans, sans-serif; color:#2a1210; font-size: 11px; margin:0; }
        .header{ display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; padding-bottom:14px; border-bottom:2px solid #55101d; }
        .brand{ font-size:16px; font-weight:bold; color:#55101d; }
        .brand small{ display:block; font-weight:normal; font-size:9px; color:#8a7a6d; margin-top:2px; }
        .invoice-meta{ text-align:right; font-size:10px; color:#6b5a4d; }
        .invoice-meta strong{ display:block; font-size:13px; color:#55101d; margin-bottom:2px; }

        .parties{ display:flex; justify-content:space-between; gap: 20px; margin-bottom: 18px; }
        .party{ width: 48%; }
        .party h4{ font-size:9px; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d; margin:0 0 4px; }
        .party p{ margin:0; line-height:1.5; }

        table{ width:100%; border-collapse:collapse; margin-bottom:18px; }
        th{ text-align:left; font-size:9px; text-transform:uppercase; color:#9c8b7d; padding:6px 0; border-bottom:1px solid #e7dcbf; }
        td{ padding:8px 0; border-bottom:1px solid #f0e9db; }
        .text-right{ text-align:right; }

        .totals{ width: 55%; margin-left: auto; }
        .totals-row{ display:flex; justify-content:space-between; padding:4px 0; font-size:11px; }
        .totals-row.grand{ border-top:1px solid #55101d; margin-top:4px; padding-top:8px; font-size:14px; font-weight:bold; color:#55101d; }

        .footer{ margin-top: 24px; font-size:9px; color:#9c8b7d; text-align:center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            Ny Herin'ny Boky
            <small>Marketplace de livres à Madagascar</small>
        </div>
        <div class="invoice-meta">
            <strong>Facture {{ $order->reference }}</strong>
            Date : {{ $order->created_at->format('d/m/Y') }}
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h4>Vendu par</h4>
            <p>
                {{ $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name }}<br>
                @if($order->seller->sellerProfile?->localisation)
                    {{ $order->seller->sellerProfile->localisation }}<br>
                @endif
                {{ $order->seller->email }}
            </p>
        </div>
        <div class="party">
            <h4>Livré à</h4>
            <p>
                {{ $order->buyer_name }}<br>
                {{ $order->adresse_livraison }}<br>
                {{ $order->ville }}<br>
                @if($order->buyer)
                    {{ $order->buyer->email }}
                @else
                    {{ $order->guest_phone }}
                    @if($order->guest_email) · {{ $order->guest_email }} @endif
                @endif
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Livre</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->book_titre }}</td>
                <td class="text-right">{{ $order->quantite }}</td>
                <td class="text-right">{{ number_format($order->prix_unitaire, 0, ',', ' ') }} Ar</td>
                <td class="text-right">{{ number_format($order->total, 0, ',', ' ') }} Ar</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Mode de paiement</span>
            <span>{{ $order->mode_paiement_label }}</span>
        </div>
        <div class="totals-row grand">
            <span>Total payé</span>
            <span>{{ number_format($order->total, 0, ',', ' ') }} Ar</span>
        </div>
    </div>

    <div class="footer">
        Ny Herin'ny Boky — madabookstore2002@gmail.com — Andranomena, Antananarivo<br>
        Facture générée automatiquement à la commande.
    </div>
</body>
</html>