<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('saudi_city_id')->nullable()->constrained('saudi_cities')->nullOnDelete();
            $table->string('office_address')->nullable();
            $table->string('business_category')->nullable();
            $table->enum('source', ['Visiting Card', 'Internet', 'Referral', 'Cold Call', 'Other'])->default('Other');
            $table->enum('status', ['New', 'Contacted', 'Interested', 'Not Interested', 'Converted to Client'])->default('New');
            $table->date('next_follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};