<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_job_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('job_category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();

            // একই worker একই category-তে দুইবার এন্ট্রি হওয়া ঠেকাতে
            $table->unique(['worker_id', 'job_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_job_categories');
    }
};