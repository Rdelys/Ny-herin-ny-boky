<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 'percent' ou 'amount' (null = pas de promotion)
            $table->string('promo_type', 10)->nullable()->after('prix_location');
            $table->unsignedInteger('promo_valeur')->nullable()->after('promo_type');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['promo_type', 'promo_valeur']);
        });
    }
};