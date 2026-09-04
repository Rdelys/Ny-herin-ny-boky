<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('auteur')->nullable()->after('titre');
            $table->enum('etat', ['neuf', 'occasion'])->default('occasion')->after('categorie');
            $table->boolean('livraison_disponible')->default(false)->after('image_path');
            $table->unsignedInteger('frais_livraison')->nullable()->after('livraison_disponible');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['auteur', 'etat', 'livraison_disponible', 'frais_livraison']);
        });
    }
};