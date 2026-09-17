{{-- ============ TABLE "MES COMMANDES" (client) / "COMMANDES REÇUES" (vendeur) ============ --}}
{{-- Inclure avec : @include('partials.orders-table', ['orders' => $orders, 'role' => 'client'|'seller'])
     Le statut n'est modifiable QUE depuis /admin/paiements : ici c'est en
     lecture seule, avec le livreur assigné dès que la commande passe
     "en livraison". --}}
@if($orders->isEmpty())
    <p style="color:#7a6a5d;">{{ __('home.order_none_yet') }}</p>
@else
    <div class="table-scroll">
        <table class="seller-table">
            <thead>
                <tr>
                    <th>{{ __('home.order_reference_label') }}</th>
                    <th>{{ __('home.book_col_title') }}</th>
                    <th>{{ $role === 'seller' ? __('home.order_buyer_label') : __('home.profile_seller_title') }}</th>
                    <th>{{ __('home.book_col_quantity') }}</th>
                    <th>{{ __('home.order_total_label') }}</th>
                    <th>Statut</th>
                    <th>{{ __('home.order_deliverer_label') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td><span class="seller-table-sub">{{ $order->reference }}</span></td>
                        <td>
                            <strong>{{ $order->book_titre }}</strong>
                        </td>
                        <td>
                            @if($role === 'seller')
                                {{ $order->buyer->name }}
                            @else
                                {{ $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name }}
                            @endif
                        </td>
                        <td>{{ $order->quantite }}</td>
                        <td>{{ number_format($order->total, 0, ',', ' ') }} Ar</td>
                        <td>
                            <span class="order-status-badge order-status-{{ $order->statut }}">{{ $order->statut_traduit }}</span>
                        </td>
                        <td>
                            @if($order->deliverer)
                                {{ $order->deliverer->nom }}<br>
                                <span class="seller-table-sub">{{ $order->deliverer->telephone }}</span>
                            @else
                                <span style="color:#96897d;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
