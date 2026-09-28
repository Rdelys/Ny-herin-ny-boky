<div class="commission-sticker commission-sticker-tiers">
                <span class="commission-sticker-pin"></span>
                <span class="commission-sticker-label">Commission selon le prix</span>
                @php $tiers = \App\Models\Setting::commissionTiers(); @endphp

                @foreach($tiers as $i => $tier)
                    @php
                        $rate = rtrim(rtrim(number_format($tier['rate'], 1, ',', ' '), '0'), ',');
                        if ($i === 0) {
                            $text = '< ' . number_format($tier['max'] + 1, 0, ',', ' ') . ' Ar';
                        } elseif ($tier['max'] === null) {
                            $text = number_format($tiers[$i - 1]['max'] + 1, 0, ',', ' ') . ' Ar+';
                        } else {
                            $text = number_format($tiers[$i - 1]['max'] + 1, 0, ',', ' ')
                                . '–' . number_format($tier['max'], 0, ',', ' ') . ' Ar';
                        }
                    @endphp
                    <span class="commission-sticker-tier">{{ $text }} : <strong>{{ $rate }}%</strong></span>
                @endforeach
            </div>