<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_agency_job_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_agency_id')->constrained('source_agencies')->cascadeOnDelete();
            $table->foreignId('job_category_id')->constrained('job_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['source_agency_id', 'job_category_id'], 'sa_job_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_agency_job_categories');
    }
};