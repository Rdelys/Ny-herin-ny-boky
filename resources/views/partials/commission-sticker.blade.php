{{-- ============ STICKER "COMMISSION ACTUELLE" ============ --}}
{{-- A inclure sur l'espace vendeur (profile/seller.blade.php), tout en haut
     du dashboard, avec : @include('partials.commission-sticker')
     Toujours à jour : lit directement Setting::commissionRate(), donc si
     l'admin change le taux, ce sticker change immédiatement lui aussi
     pour tous les vendeurs connectés, sans rien à faire côté code. --}}
@php
    $stickerRate = \App\Models\Setting::commissionRate();
    $stickerRateDisplay = rtrim(rtrim(number_format($stickerRate, 2, ',', ' '), '0'), ',');
@endphp

<div class="commission-sticker">
    <span class="commission-sticker-pin"></span>
    <span class="commission-sticker-label">Commission actuelle</span>
    <strong class="commission-sticker-rate">{{ $stickerRateDisplay }}%</strong>
</div>