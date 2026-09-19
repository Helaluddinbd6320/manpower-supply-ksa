<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone_number');
            $table->foreignId('destination_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('job_category_id')->nullable()->constrained('job_categories')->nullOnDelete();

            $table->unsignedTinyInteger('age')->nullable();
            $table->string('area')->nullable();

            $table->enum('source', ['WhatsApp Inbound', 'Referral', 'Facebook', 'Walk-in', 'Other'])
                ->default('WhatsApp Inbound');

            $table->enum('status', [
                'New',
                'Contacted',
                'Interested',
                'Documents Collecting',
                'Not Interested',
                'Converted to Worker',
            ])->default('New');

            $table->date('next_follow_up_date')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_leads');
    }
};