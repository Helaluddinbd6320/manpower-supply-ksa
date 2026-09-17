<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('client_company_name');
            $table->date('quotation_date');

            $table->string('header_image_path')->nullable();
            $table->longText('client_info_content')->nullable();
            $table->longText('terms_content')->nullable();
            $table->longText('signature_content')->nullable();
            $table->longText('footer_content')->nullable();

            $table->enum('status', ['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])->default('Draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};