<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_leads', function (Blueprint $table) {
            $table->foreignId('nationality_country_id')
                ->nullable()
                ->after('age')
                ->constrained('countries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidate_leads', function (Blueprint $table) {
            $table->dropForeign(['nationality_country_id']);
            $table->dropColumn('nationality_country_id');
        });
    }
};