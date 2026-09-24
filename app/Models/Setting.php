<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const PAYMENT_METHODS = [
        'mvola'   => 'MVola',
        'orange'  => 'Orange Money',
        'airtel'  => 'Airtel Money',
        'especes' => 'Espèces à la livraison',
    ];

    public static function get(string $key, $default = null)
    {
        return optional(static::where('key', $key)->first())->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget('commission_tiers');
        Cache::forget('payment_accounts');
    }

    /** @return array<int, array{max:?int, rate:float}> */
    public static function commissionTiers(): array
    {
        return Cache::remember('commission_tiers', 3600, function () {
            return [
                ['max' => (int)   static::get('commission_tier1_max',  59999),
                 'rate'=> (float) static::get('commission_tier1_rate', 10.0)],
                ['max' => (int)   static::get('commission_tier2_max',  99999),
                 'rate'=> (float) static::get('commission_tier2_rate', 8.0)],
                ['max' => null,
                 'rate'=> (float) static::get('commission_tier3_rate', 5.0)],
            ];
        });
    }

    /** Taux réel pour un montant donné. */
    public static function commissionRateFor(?int $montant): float
    {
        $montant = (int) $montant;

        foreach (static::commissionTiers() as $tier) {
            if ($tier['max'] === null || $montant <= $tier['max']) {
                return $tier['rate'];
            }
        }

        return (float) static::get('commission_tier3_rate', 5.0);
    }

    /**
     * Taux « d'accroche » pour l'affichage (modal d'inscription…).
     * = taux du palier 1, celui qu'un nouveau vendeur verra en premier.
     */
    public static function commissionRate(): float
    {
        return (float) static::commissionTiers()[0]['rate'];
    }

    /** @return array<string, array{label:string, numero:string, nom:string}> */
    public static function paymentAccounts(): array
    {
        return Cache::remember('payment_accounts', 3600, function () {
            $values = static::query()
                ->where('key', 'like', 'payment_%')
                ->pluck('value', 'key');

            $accounts = [];

            foreach (self::PAYMENT_METHODS as $key => $label) {
                $accounts[$key] = [
                    'label'  => $label,
                    'numero' => (string) ($values['payment_' . $key . '_number'] ?? ''),
                    'nom'    => (string) ($values['payment_' . $key . '_name'] ?? ''),
                ];
            }

            return $accounts;
        });
    }

    /** Villes desservies ; seule Antananarivo autorise le paiement espèces. */
    public const VILLES = [
        'Antananarivo',
        'Antsiranana',
        'Mahajanga',
        'Toamasina',
        'Toliara',
        'Fianarantsoa',
    ];

    public const VILLE_ESPECES = 'Antananarivo';
}