<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Provinces / villes desservies. `est_capitale` = quartiers + VIP + espèces.
        Schema::create('delivery_zones', function (Blueprint $t) {
            $t->id();
            $t->string('nom')->unique();
            $t->boolean('est_capitale')->default(false);
            $t->unsignedInteger('frais')->default(0);          // frais prépayés (hors taxi-brousse)
            $t->unsignedInteger('delai_min_h')->default(24);
            $t->unsignedInteger('delai_max_h')->default(72);
            $t->boolean('actif')->default(true);
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('delivery_quartiers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('zone_id')->constrained('delivery_zones')->cascadeOnDelete();
            $t->string('nom');
            $t->unsignedInteger('frais')->default(0);
            $t->boolean('actif')->default(true);
            $t->timestamps();
            $t->unique(['zone_id', 'nom']);
        });

        Schema::create('delivery_cooperatives', function (Blueprint $t) {
            $t->id();
            $t->foreignId('zone_id')->constrained('delivery_zones')->cascadeOnDelete();
            $t->string('nom');
            $t->string('telephone', 40)->nullable();
            $t->string('note')->nullable();
            $t->boolean('actif')->default(true);
            $t->timestamps();
            $t->unique(['zone_id', 'nom']);
        });

        // Une livraison par panier (liée aux commandes via groupe_reference).
        // On copie les noms/prix : l'historique ne bouge pas si l'admin modifie les tarifs.
        Schema::create('deliveries', function (Blueprint $t) {
            $t->id();
            $t->string('groupe_reference')->unique();
            $t->string('type', 12)->default('standard');       // standard | vip
            $t->foreignId('zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $t->string('zone_nom');
            $t->foreignId('quartier_id')->nullable()->constrained('delivery_quartiers')->nullOnDelete();
            $t->string('quartier_nom')->nullable();
            $t->boolean('quartier_personnalise')->default(false);   // saisi via « Autre »
            $t->foreignId('cooperative_id')->nullable()->constrained('delivery_cooperatives')->nullOnDelete();
            $t->string('cooperative_nom')->nullable();
            $t->boolean('cooperative_personnalisee')->default(false);
            $t->unsignedInteger('frais_base')->default(0);
            $t->unsignedInteger('supplement_vip')->default(0);
            $t->unsignedInteger('frais')->default(0);               // base + supplément VIP, payé en ligne
            $t->boolean('frais_gratuit')->default(false);
            $t->boolean('frais_a_confirmer')->default(false);
            $t->boolean('taxi_brousse_pa')->default(false);
            $t->dateTime('heure_prevue')->nullable();
            $t->unsignedInteger('delai_min_h')->default(24);
            $t->unsignedInteger('delai_max_h')->default(72);
            $t->timestamps();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->boolean('is_platform')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_platform'));
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('delivery_cooperatives');
        Schema::dropIfExists('delivery_quartiers');
        Schema::dropIfExists('delivery_zones');
    }
};