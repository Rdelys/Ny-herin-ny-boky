<?php
// database/migrations/xxxx_make_orders_guest_friendly.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // buyer_id devient nullable : une commande "invité" n'a pas de compte.
            $table->foreignId('buyer_id')->nullable()->change();

            $table->string('guest_name')->nullable()->after('buyer_id');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->string('guest_email')->nullable()->after('guest_phone');
            $table->string('guest_address')->nullable()->after('guest_email');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_email', 'guest_address']);
            $table->foreignId('buyer_id')->nullable(false)->change();
        });
    }
};