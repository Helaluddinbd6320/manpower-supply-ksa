<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->restrictOnDelete();

            $table->foreignId('job_order_id')
                ->constrained('job_orders')
                ->restrictOnDelete();

            $table->string('client_company_name');

            $table->date('start_date');
            $table->date('contract_end_date')->nullable();

            $table->decimal('client_billing_rate', 10, 2);
            $table->decimal('worker_payout_rate', 10, 2);

            $table->string('status')->default('active');

            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['job_order_id', 'worker_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};