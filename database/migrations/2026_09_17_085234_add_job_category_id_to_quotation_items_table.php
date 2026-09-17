<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('job_category_id')
                ->nullable()
                ->after('id')
                ->constrained('job_categories')
                ->nullOnDelete();

            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('category')->after('quotation_id');

            $table->dropForeign(['job_category_id']);
            $table->dropColumn('job_category_id');
        });
    }
};