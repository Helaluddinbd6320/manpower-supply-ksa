<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->decimal('client_billing_rate', 10, 2)->nullable()->change();
            $table->decimal('worker_payout_rate', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->decimal('client_billing_rate', 10, 2)->nullable(false)->change();
            $table->decimal('worker_payout_rate', 10, 2)->nullable(false)->change();
        });
    }
};