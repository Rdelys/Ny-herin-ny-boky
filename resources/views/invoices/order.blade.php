@php
    $order = $orders->first();
    $vendeurs = $orders->pluck('seller')->unique('id');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 14mm 12mm; }
        body{ font-family: DejaVu Sans, sans-serif; color:#2a1210; font-size: 11px; margin:0; }
        table{ width:100%; border-collapse:collapse; }

        .header{ margin-bottom:18px; }
        .header td{ padding-bottom:14px; border-bottom:2px solid #55101d; vertical-align:top; }
        .brand{ font-size:16px; font-weight:bold; color:#55101d; }
        .brand small{ display:block; font-weight:normal; font-size:9px; color:#8a7a6d; margin-top:2px; }
        .invoice-meta{ text-align:right; font-size:10px; color:#6b5a4d; }
        .invoice-meta strong{ display:block; font-size:13px; color:#55101d; margin-bottom:2px; }

        .parties{ margin-bottom:18px; }
        .parties td{ width:50%; vertical-align:top; padding-right:12px; }
        .parties h4{ font-size:9px; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d; margin:0 0 4px; }
        .parties p{ margin:0 0 6px; line-height:1.5; }

        .lines{ margin-bottom:18px; }
        .lines th{ text-align:left; font-size:9px; text-transform:uppercase; color:#9c8b7d; padding:6px 0; border-bottom:1px solid #e7dcbf; }
        .lines td{ padding:8px 0; border-bottom:1px solid #f0e9db; vertical-align:top; }
        .lines small{ display:block; font-size:8px; color:#9c8b7d; margin-top:2px; }
        .text-right{ text-align:right; }

        .totals{ width:60%; }
        .totals td{ padding:4px 0; font-size:11px; }
        .totals .grand td{ border-top:1px solid #55101d; padding-top:8px; font-size:14px; font-weight:bold; color:#55101d; }

        .footer{ margin-top:24px; font-size:9px; color:#9c8b7d; text-align:center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="brand">
                Ny Herin'ny Boky
                <small>Marketplace de livres à Madagascar</small>
            </td>
            <td class="invoice-meta">
                <strong>Facture {{ $numero }}</strong>
                Date : {{ $order->created_at->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <h4>Vendu par</h4>
                @foreach($vendeurs as $vendeur)
                    <p>
                        {{ $vendeur->sellerProfile->nom_entreprise ?? $vendeur->name }}<br>
                        @if($vendeur->sellerProfile?->localisation)
                            {{ $vendeur->sellerProfile->localisation }}<br>
                        @endif
                        {{ $vendeur->email }}
                    </p>
                @endforeach
            </td>
            <td>
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
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>Livre</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $ligne)
                <tr>
                    <td>
                        {{ $ligne->book_titre }}
                        <small>{{ $ligne->seller->sellerProfile->nom_entreprise ?? $ligne->seller->name }} · {{ $ligne->reference }}</small>
                    </td>
                    <td class="text-right">{{ $ligne->quantite }}</td>
                    <td class="text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
                    <td class="text-right">{{ number_format($ligne->total, 0, ',', ' ') }} Ar</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals" align="right">
        <tr>
            <td>Mode de paiement</td>
            <td class="text-right">{{ $order->mode_paiement_label }}</td>
        </tr>
        <tr class="grand">
            <td>Total payé</td>
            <td class="text-right">{{ number_format($orders->sum('total'), 0, ',', ' ') }} Ar</td>
        </tr>
    </table>

    <div class="footer" style="clear:both;">
        Ny Herin'ny Boky — madabookstore2002@gmail.com — Andranomena, Antananarivo<br>
        Facture générée automatiquement à la commande.
    </div>
</body>
</html>