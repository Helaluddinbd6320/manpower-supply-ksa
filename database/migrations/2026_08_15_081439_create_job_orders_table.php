<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_category_id')
                ->constrained('job_categories')
                ->restrictOnDelete();

            $table->string('company_name');
            $table->unsignedInteger('quantity_needed');

            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();

            $table->unsignedInteger('contract_duration_months')->nullable();
            $table->text('requirements')->nullable();

            $table->date('order_date');
            $table->date('deadline')->nullable();

            $table->string('status')->default('open');

            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};