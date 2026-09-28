<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * 1. Passer temporairement etat en VARCHAR
         * pour pouvoir modifier librement les anciennes valeurs.
         */
        Schema::table('books', function (Blueprint $table) {
            $table->string('etat', 30)->default('bon_etat')->change();
        });

        /*
         * 2. Convertir les anciennes valeurs
         */
        DB::table('books')
            ->where('etat', 'occasion')
            ->update(['etat' => 'bon_etat']);

        /*
         * Si certaines anciennes lignes ont une valeur vide,
         * on les convertit également.
         */
        DB::table('books')
            ->where('etat', '')
            ->update(['etat' => 'bon_etat']);

        /*
         * 3. Maintenant que les données sont propres,
         * remettre le ENUM définitif.
         */
        Schema::table('books', function (Blueprint $table) {
            $table->enum('etat', [
                'neuf',
                'tres_bon_etat',
                'bon_etat'
            ])->default('bon_etat')->change();
        });

        /*
         * 4. Supprimer frais_livraison
         */
        if (Schema::hasColumn('books', 'frais_livraison')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('frais_livraison');
            });
        }
    }

    public function down(): void
    {
        /*
         * 1. Passer temporairement en VARCHAR
         */
        Schema::table('books', function (Blueprint $table) {
            $table->string('etat', 30)->default('occasion')->change();
        });

        /*
         * 2. Reconvertir les nouveaux états
         */
        DB::table('books')
            ->whereIn('etat', ['tres_bon_etat', 'bon_etat'])
            ->update(['etat' => 'occasion']);

        /*
         * 3. Revenir à l'ancien ENUM
         */
        Schema::table('books', function (Blueprint $table) {
            $table->enum('etat', [
                'neuf',
                'occasion'
            ])->default('occasion')->change();
        });

        /*
         * 4. Restaurer frais_livraison
         */
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedInteger('frais_livraison')
                ->nullable()
                ->after('livraison_disponible');
        });
    }
};