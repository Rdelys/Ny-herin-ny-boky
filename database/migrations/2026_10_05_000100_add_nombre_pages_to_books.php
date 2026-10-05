<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $t) {
            $t->unsignedSmallInteger('nombre_pages')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $t) {
            $t->dropColumn('nombre_pages');
        });
    }
};