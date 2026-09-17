<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();

            $table->string('category');
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable();

            $table->enum('pricing_type', ['hourly', 'monthly'])->default('monthly');
            $table->decimal('hours_per_day', 5, 2)->nullable();
            $table->decimal('rate_per_hour', 10, 2)->nullable();

            $table->unsignedInteger('qty');
            $table->decimal('monthly_rate_per_worker', 10, 2);

            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};