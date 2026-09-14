<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_monthly_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('placement_id')
                ->constrained('placements')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');

            $table->unsignedTinyInteger('duty_days');

            // Snapshot of rates used for this month's calculation
            // (protects historical accuracy if placement rates change later).
            $table->decimal('client_billing_rate_snapshot', 10, 2)->nullable();
            $table->decimal('worker_payout_rate_snapshot', 10, 2)->nullable();

            // Prorated calculated amounts: (duty_days / days_in_month) * rate
            $table->decimal('client_amount', 10, 2)->nullable();
            $table->decimal('worker_amount', 10, 2)->nullable();

            $table->string('client_payment_status')->default('pending');
            $table->timestamp('client_paid_at')->nullable();

            $table->string('worker_payment_status')->default('pending');
            $table->timestamp('worker_paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['placement_id', 'month', 'year']);
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_monthly_records');
    }
};