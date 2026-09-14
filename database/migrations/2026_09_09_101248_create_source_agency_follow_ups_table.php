<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_agency_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_agency_id')->constrained('source_agencies')->cascadeOnDelete();
            $table->foreignId('contacted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('contacted_at');
            $table->text('note')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_agency_follow_ups');
    }
};