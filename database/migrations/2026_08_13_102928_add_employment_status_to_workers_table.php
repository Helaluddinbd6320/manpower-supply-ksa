<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Employment status
            $table->enum('employment_status', ['free_available', 'currently_working'])
                ->default('free_available')
                ->after('kafala_transfer_interested'); // adjust "after" to match the last column from Step 14 (Location Status) in your actual migration

            // Fields relevant when employment_status = currently_working
            $table->date('contract_end_date')->nullable()->after('employment_status');
            $table->text('change_reason')->nullable()->after('contract_end_date');
            $table->string('notice_period')->nullable()->after('change_reason');

            // Fields relevant when employment_status = free_available
            $table->date('available_from_date')->nullable()->after('notice_period');
            $table->decimal('expected_salary', 10, 2)->nullable()->after('available_from_date');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn([
                'employment_status',
                'contract_end_date',
                'change_reason',
                'notice_period',
                'available_from_date',
                'expected_salary',
            ]);
        });
    }
};