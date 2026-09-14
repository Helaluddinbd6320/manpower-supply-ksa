<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_type'); // cv, photo, passport, iqama, certificate

            $table->string('file_path'); // R2 object key, e.g. workers/CHI-2026-0001/passport.jpg
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('mime_type')->nullable();

            $table->string('label')->nullable(); // e.g. "Welding Level 2 Certificate"

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['worker_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_documents');
    }
};