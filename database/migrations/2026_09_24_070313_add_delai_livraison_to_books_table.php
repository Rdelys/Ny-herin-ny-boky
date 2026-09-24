<?php
// database/migrations/xxxx_add_delai_livraison_to_books_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 1/1 = déjà disponible localement, livraison ~24h.
            // Des valeurs plus hautes couvrent les livres importés de l'étranger.
            $table->unsignedTinyInteger('delai_livraison_min')->default(1)->after('livraison_disponible');
            $table->unsignedTinyInteger('delai_livraison_max')->default(1)->after('delai_livraison_min');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['delai_livraison_min', 'delai_livraison_max']);
        });
    }
};