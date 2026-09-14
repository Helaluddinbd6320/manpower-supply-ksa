<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_order_worker', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_order_id')
                ->constrained('job_orders')
                ->cascadeOnDelete();

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->cascadeOnDelete();

            $table->string('status')->default('shortlisted');

            $table->timestamp('contacted_at')->nullable();
            $table->foreignId('contacted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['job_order_id', 'worker_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_order_worker');
    }
};