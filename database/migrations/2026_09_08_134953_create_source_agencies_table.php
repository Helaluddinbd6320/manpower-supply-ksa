<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('agency_name');
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('contact_person')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('office_address')->nullable();
            $table->string('license_number')->nullable();
            $table->enum('source', ['Visiting Card', 'Internet', 'Referral', 'Cold Call', 'Other'])->default('Other');
            $table->enum('status', ['New', 'Contacted', 'Active Partner', 'Not Interested'])->default('New');
            $table->date('next_follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_agencies');
    }
};