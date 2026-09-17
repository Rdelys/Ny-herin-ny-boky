<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();

            // Copie du titre : la commande reste lisible même si le vendeur
            // supprime le livre plus tard.
            $table->string('book_titre');

            $table->unsignedInteger('quantite')->default(1);
            $table->unsignedInteger('prix_unitaire');   // prix client (commission incluse)
            $table->unsignedInteger('total');
            $table->decimal('commission_rate', 5, 2);   // taux figé au moment de la commande
            $table->unsignedInteger('montant_vendeur'); // ce que le vendeur perçoit

            $table->string('mode_paiement');            // mvola | orange | airtel
            $table->string('reference_paiement');       // référence du SMS saisie par le client

            $table->string('statut')->default('en_attente_livraison');
            $table->foreignId('deliverer_id')->nullable()->constrained('deliverers')->nullOnDelete();
            $table->timestamp('livree_at')->nullable();

            $table->timestamps();

            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
