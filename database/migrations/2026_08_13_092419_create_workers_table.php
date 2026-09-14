<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            // System-generated unique code, format: CHI-YYYY-XXXX (generated in Worker model, Step 12)
            $table->string('worker_id')->unique();

            // Basic Info
            $table->string('name');
            $table->string('passport_number')->unique();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('religion')->nullable();
            $table->string('nationality')->default('Bangladesh');
            $table->string('mobile_number');
            $table->string('emergency_contact_number')->nullable();
            $table->string('district')->nullable();
            $table->string('upazila')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();

            // Workflow status (system info)
            $table->enum('workflow_status', [
                'available',
                'shortlisted',
                'interview',
                'selected',
                'placed',
            ])->default('available');

            // Audit trail — which staff entered this record
            $table->foreignId('entry_staff_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes for common filters
            $table->index('workflow_status');
            $table->index('nationality');
            $table->index('gender');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};