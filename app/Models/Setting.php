<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Opérateurs mobile money acceptés, avec leur libellé affiché. */
    public const PAYMENT_METHODS = [
        'mvola' => 'MVola',
        'orange' => 'Orange Money',
        'airtel' => 'Airtel Money',
    ];

    public static function get(string $key, $default = null)
    {
        return optional(static::where('key', $key)->first())->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget('commission_rate');
        Cache::forget('payment_accounts');
    }

    /**
     * Taux de commission de la plateforme, en pourcentage (ex: 10 pour 10%).
     * Point d'entrée UNIQUE utilisé partout où ce taux doit apparaître
     * (prix client, sticker vendeur, modal d'inscription vendeur...).
     * Mis en cache 1h pour éviter une requête à chaque calcul de prix.
     */
    public static function commissionRate(): float
    {
        return (float) Cache::remember('commission_rate', 3600, function () {
            return static::get('commission_rate', 10);
        });
    }

    /**
     * Comptes mobile money de la plateforme : pour chaque opérateur, le
     * numéro ET le nom du titulaire de la puce (c'est ce nom que le client
     * doit voir avant de payer, pour être sûr d'envoyer au bon compte).
     * Réglables depuis /admin/parametres.
     *
     * @return array<string, array{label:string, numero:string, nom:string}>
     */
    public static function paymentAccounts(): array
    {
        return Cache::remember('payment_accounts', 3600, function () {
            $values = static::query()
                ->where('key', 'like', 'payment_%')
                ->pluck('value', 'key');

            $accounts = [];

            foreach (self::PAYMENT_METHODS as $key => $label) {
                $accounts[$key] = [
                    'label' => $label,
                    'numero' => (string) ($values['payment_' . $key . '_number'] ?? ''),
                    'nom' => (string) ($values['payment_' . $key . '_name'] ?? ''),
                ];
            }

            return $accounts;
        });
    }
}
