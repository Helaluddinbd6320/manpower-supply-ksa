<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_template_blocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['header_image', 'client_info', 'terms', 'signature', 'footer']);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_template_blocks');
    }
};