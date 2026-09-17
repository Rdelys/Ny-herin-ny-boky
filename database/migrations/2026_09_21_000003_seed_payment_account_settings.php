<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Numéros "mobile money" de la plateforme + nom du titulaire de la puce.
 * Stockés dans `settings` (même mécanique que commission_rate) pour que
 * l'admin puisse les changer depuis /admin/parametres sans toucher au code.
 *
 * Les valeurs par défaut reprennent les numéros qui étaient écrits en dur
 * dans la modal de commande (layouts/app.blade.php).
 */
return new class extends Migration
{
    private const DEFAULTS = [
        'payment_mvola_number' => '034 41 266 44',
        'payment_mvola_name' => '',
        'payment_orange_number' => '032 41 266 44',
        'payment_orange_name' => '',
        'payment_airtel_number' => '033 41 266 44',
        'payment_airtel_name' => '',
    ];

    public function up(): void
    {
        $now = now();

        foreach (self::DEFAULTS as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => $now, 'updated_at' => $now],
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_keys(self::DEFAULTS))->delete();
    }
};
