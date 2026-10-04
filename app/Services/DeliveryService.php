<?php

namespace App\Services;

use App\Models\DeliveryCooperative;
use App\Models\DeliveryQuartier;
use App\Models\DeliveryZone;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    public const TYPE_STANDARD = 'standard';
    public const TYPE_VIP = 'vip';
    public const AUTRE = 'autre';

    /** Marge (minutes) sur la fenêtre VIP : la page peut rester ouverte quelques minutes. */
    private const SLOT_TOLERANCE = 20;

    /** Délai cumulé du panier en heures : le livre le plus lent décide. */
    public static function lead(Collection $books): array
    {
        return [
            'min_h' => max(1, (int) ($books->max('delai_livraison_min') ?? 1)) * 24,
            'max_h' => max(1, (int) ($books->max('delai_livraison_max') ?? 1)) * 24,
        ];
    }

    /** Livraison offerte seulement si TOUS les livres viennent du compte officiel. */
    public static function allFromPlatform(Collection $books): bool
    {
        return $books->isNotEmpty() && $books->every(fn ($b) => (bool) $b->seller?->is_platform);
    }

    public static function rangeLabel(int $min, int $max): string
    {
        if ($max <= 72) {
            return __('home.delivery_range_hours', ['min' => $min, 'max' => $max]);
        }

        $dMin = (int) ceil($min / 24);
        $dMax = (int) ceil($max / 24);

        return $dMin === $dMax
            ? __('home.delivery_range_days_one', ['n' => $dMax])
            : __('home.delivery_range_days', ['min' => $dMin, 'max' => $dMax]);
    }

    /** Créneaux VIP (toutes les 30 min) dans la fenêtre « maintenant + 5 h → + 12 h », heures d'ouverture. */
    public static function vipSlots(array $s): array
    {
        $to = now()->addHours($s['vip_max_h']);
        $slot = now()->addHours($s['vip_min_h'])->second(0)->microsecond(0);

        $m = (int) $slot->minute;
        if ($m > 0 && $m <= 30) {
            $slot->minute(30);
        } elseif ($m > 30) {
            $slot->addHour()->minute(0);
        }

        $slots = [];
        while ($slot->lte($to)) {
            if ($slot->hour >= $s['vip_open_hour'] && $slot->hour < $s['vip_close_hour']) {
                $slots[] = [
                    'value' => $slot->format('Y-m-d H:i'),
                    'label' => ($slot->isToday() ? __('home.delivery_today') : __('home.delivery_tomorrow'))
                        . ' · ' . $slot->format('H:i'),
                ];
            }
            $slot->addMinutes(30);
        }

        return $slots;
    }

    /** Tout ce dont le JS du checkout a besoin (aperçu des prix en direct). */
    public static function config(Collection $books): array
    {
        $s = Setting::deliverySettings();
        $lead = self::lead($books);

        $zones = DeliveryZone::actifs()
            ->with([
                'quartiers' => fn ($q) => $q->where('actif', true)->orderBy('nom'),
                'cooperatives' => fn ($q) => $q->where('actif', true)->orderBy('nom'),
            ])
            ->orderBy('position')->orderBy('nom')
            ->get();

        return [
            'free' => self::allFromPlatform($books),
            'longDelay' => $lead['max_h'] > $s['standard_max_h'],
            'standardMaxH' => $s['standard_max_h'],
            'leadLabel' => self::rangeLabel($lead['min_h'], $lead['max_h']),
            'vip' => [
                'enabled' => $s['vip_actif'],
                'leadOk' => $lead['max_h'] <= $s['vip_max_lead_h'],
                'maxLeadH' => $s['vip_max_lead_h'],
                'surcharge' => $s['vip_surcharge'],
                'label' => self::rangeLabel($s['vip_min_h'], $s['vip_max_h']),
                'slots' => self::vipSlots($s),
            ],
            'zones' => $zones->map(fn ($z) => [
                'id' => $z->id,
                'nom' => $z->nom,
                'capitale' => $z->est_capitale,
                'frais' => (int) $z->frais,
                'label' => self::rangeLabel(
                    max($z->delai_min_h, $lead['min_h']),
                    max($z->delai_max_h, $lead['max_h'])
                ),
                'quartiers' => $z->quartiers->map(fn ($q) => ['id' => $q->id, 'nom' => $q->nom, 'frais' => (int) $q->frais])->values(),
                'cooperatives' => $z->cooperatives->map(fn ($c) => ['id' => $c->id, 'nom' => $c->nom])->values(),
            ])->values(),
        ];
    }

    /**
     * Valide le choix du client et retourne les attributs d'une ligne `deliveries`.
     * Les montants sont TOUJOURS recalculés ici, jamais lus depuis le formulaire.
     */
    public static function resolve(array $in, Collection $books): array
    {
        $s = Setting::deliverySettings();
        $lead = self::lead($books);
        $free = self::allFromPlatform($books);

        $zone = DeliveryZone::actifs()->where('nom', $in['ville'] ?? '')->first();
        if (! $zone) {
            self::fail('ville', __('home.delivery_error_zone'));
        }

        $type = ($in['livraison_type'] ?? '') === self::TYPE_VIP ? self::TYPE_VIP : self::TYPE_STANDARD;

        $d = [
            'quartier_id' => null, 'quartier_nom' => null, 'quartier_personnalise' => false,
            'cooperative_id' => null, 'cooperative_nom' => null, 'cooperative_personnalisee' => false,
            'taxi_brousse_pa' => false,
        ];
        $base = 0;
        $aConfirmer = false;

        if ($zone->est_capitale) {
            $choix = (string) ($in['quartier_id'] ?? '');

            if ($choix === self::AUTRE) {
                $nom = trim((string) ($in['quartier_autre'] ?? ''));
                if ($nom === '') {
                    self::fail('quartier_autre', __('home.delivery_error_quartier'));
                }
                $d['quartier_nom'] = $nom;
                $d['quartier_personnalise'] = true;
                $aConfirmer = true; // l'admin fixera le tarif
            } else {
                $q = DeliveryQuartier::where('zone_id', $zone->id)->where('actif', true)->find($choix);
                if (! $q) {
                    self::fail('quartier_id', __('home.delivery_error_quartier'));
                }
                $d['quartier_id'] = $q->id;
                $d['quartier_nom'] = $q->nom;
                $base = (int) $q->frais;
            }
        } else {
            $choix = (string) ($in['cooperative_id'] ?? '');

            if ($choix === self::AUTRE) {
                $nom = trim((string) ($in['cooperative_autre'] ?? ''));
                if ($nom === '') {
                    self::fail('cooperative_autre', __('home.delivery_error_coop'));
                }
                $d['cooperative_nom'] = $nom;
                $d['cooperative_personnalisee'] = true;
            } else {
                $c = DeliveryCooperative::where('zone_id', $zone->id)->where('actif', true)->find($choix);
                if (! $c) {
                    self::fail('cooperative_id', __('home.delivery_error_coop'));
                }
                $d['cooperative_id'] = $c->id;
                $d['cooperative_nom'] = $c->nom;
            }

            $base = (int) $zone->frais;
            $d['taxi_brousse_pa'] = true; // frais de la coopérative : payés à l'arrivée
        }

        if ($free) {
            $base = 0;
            $aConfirmer = false;
        }

        $window = [max($zone->delai_min_h, $lead['min_h']), max($zone->delai_max_h, $lead['max_h'])];
        $supplement = 0;
        $heure = null;

        if ($type === self::TYPE_VIP) {
            if (! $zone->est_capitale) {
                self::fail('livraison_type', __('home.delivery_vip_only_tana'));
            }
            if (! $s['vip_actif']) {
                self::fail('livraison_type', __('home.delivery_vip_off'));
            }
            if ($lead['max_h'] > $s['vip_max_lead_h']) {
                self::fail('livraison_type', __('home.delivery_vip_unavailable_lead', ['hours' => $s['vip_max_lead_h']]));
            }

            $heure = self::parseSlot($in['heure_prevue'] ?? null, $s);
            $supplement = $s['vip_surcharge'];
            $window = [$s['vip_min_h'], $s['vip_max_h']];
        }

        return $d + [
            'type' => $type,
            'zone_id' => $zone->id,
            'zone_nom' => $zone->nom,
            'frais_base' => $base,
            'supplement_vip' => $supplement,
            'frais' => $base + $supplement,
            'frais_gratuit' => $free,
            'frais_a_confirmer' => $aConfirmer,
            'heure_prevue' => $heure,
            'delai_min_h' => $window[0],
            'delai_max_h' => $window[1],
        ];
    }

    private static function parseSlot(?string $value, array $s): Carbon
    {
        try {
            $slot = Carbon::createFromFormat('Y-m-d H:i', (string) $value);
        } catch (\Throwable $e) {
            $slot = null;
        }

        if (! $slot) {
            self::fail('heure_prevue', __('home.delivery_error_vip_slot'));
        }

        $lo = now()->addHours($s['vip_min_h'])->subMinutes(self::SLOT_TOLERANCE);
        $hi = now()->addHours($s['vip_max_h'])->addMinutes(self::SLOT_TOLERANCE);

        if ($slot->lt($lo) || $slot->gt($hi)
            || $slot->hour < $s['vip_open_hour'] || $slot->hour >= $s['vip_close_hour']) {
            self::fail('heure_prevue', __('home.delivery_error_vip_slot'));
        }

        return $slot;
    }

    private static function fail(string $field, string $message): void
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}