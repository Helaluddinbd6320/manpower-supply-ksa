<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->foreignId('sourcing_agent_id')
                ->nullable()
                ->after('entered_by')
                ->constrained('agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropForeign(['sourcing_agent_id']);
            $table->dropColumn('sourcing_agent_id');
        });
    }
};