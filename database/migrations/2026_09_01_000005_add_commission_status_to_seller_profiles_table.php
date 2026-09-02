<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            // Statut de la commission appliquée au vendeur, ex: "10%".
            // Pour l'instant seul le mode "commission" est actif (abonnement désactivé).
            $table->string('commission_status')->default('10%')->after('mode_paiement');
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn('commission_status');
        });
    }
};