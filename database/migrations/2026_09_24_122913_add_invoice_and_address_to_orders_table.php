<?php
// database/migrations/xxxx_add_invoice_and_address_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Adresse de livraison : maintenant demandée à TOUT le monde,
            // client connecté ou invité (remplace guest_address).
            $table->renameColumn('guest_address', 'adresse_livraison');
            $table->string('facture_path')->nullable()->after('adresse_livraison');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('facture_path');
            $table->renameColumn('adresse_livraison', 'guest_address');
        });
    }
};