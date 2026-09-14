<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('passport_number')->nullable()->change();
            $table->date('passport_issue_date')->nullable()->change();
            $table->date('passport_expiry_date')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('marital_status')->nullable()->change();
            $table->string('location_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('passport_number')->nullable(false)->change();
            $table->date('passport_issue_date')->nullable(false)->change();
            $table->date('passport_expiry_date')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('marital_status')->nullable(false)->change();
            $table->string('location_type')->nullable(false)->change();
        });
    }
};