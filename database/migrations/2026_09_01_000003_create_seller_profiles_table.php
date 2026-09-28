<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nom_entreprise');
            $table->string('localisation')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('numero_paiement')->nullable(); // ex: 034 xx xxx xx (Mvola/Orange Money...)
            $table->enum('mode_paiement', ['commission', 'abonnement'])->default('commission');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};