<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return optional(static::where('key', $key)->first())->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('commission_rate');
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
}