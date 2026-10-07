<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequences', function (Blueprint $t) {
            $t->string('nom')->primary();
            $t->unsignedBigInteger('valeur')->default(0);
        });

        // Prochaine commande = NHB000001. Pour démarrer plus haut, mets la valeur de départ ici.
        DB::table('sequences')->insert(['nom' => 'commande', 'valeur' => 0]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};