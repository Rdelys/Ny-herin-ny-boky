<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('localisation')->nullable();
            $table->string('motif_inscription')->nullable();       // "Pourquoi vous inscrivez-vous ?"
            $table->string('types_livres_recherches')->nullable(); // genre de livres recherchés
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_profiles');
    }
};