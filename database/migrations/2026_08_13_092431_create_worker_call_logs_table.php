<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_call_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->cascadeOnDelete();

            // Which staff made the contact
            $table->foreignId('called_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('called_at');

            $table->enum('contact_method', [
                'phone_call',
                'whatsapp',
                'sms',
                'in_person',
            ])->default('phone_call');

            $table->enum('outcome', [
                'reached',
                'not_reached',
                'interested',
                'not_interested',
                'no_response',
                'other',
            ]);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['worker_id', 'called_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_call_logs');
    }
};