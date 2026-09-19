<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_leads', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('area');
            $table->string('passport_copy_path')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_leads', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'passport_copy_path']);
        });
    }
};