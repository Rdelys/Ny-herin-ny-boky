<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Reversement de l'argent au vendeur : l'admin bascule « dû » ->
            // « envoyé » depuis /admin/paiements une fois le transfert fait.
            $table->string('paiement_vendeur')->default('du')->after('livree_at');
            $table->timestamp('paiement_vendeur_at')->nullable()->after('paiement_vendeur');

            $table->index('paiement_vendeur');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['paiement_vendeur']);
            $table->dropColumn(['paiement_vendeur', 'paiement_vendeur_at']);
        });
    }
};
