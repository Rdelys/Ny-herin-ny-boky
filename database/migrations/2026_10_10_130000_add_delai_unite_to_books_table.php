<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 'heures' ou 'jours' : les livres existants restent en jours.
            $table->string('delai_livraison_unite', 10)->default('jours')->after('delai_livraison_max');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('delai_livraison_unite');
        });
    }
};