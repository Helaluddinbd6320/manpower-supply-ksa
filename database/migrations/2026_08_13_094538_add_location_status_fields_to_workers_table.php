<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Location Type: pre_departure (in Bangladesh) / in_saudi_arabia (working)
            $table->string('location_type')->default('pre_departure')->after('marital_status');

            // Iqama-related fields — only relevant when location_type = in_saudi_arabia
            $table->string('iqama_number')->nullable()->after('location_type');
            $table->string('iqama_photo_path')->nullable()->after('iqama_number');
            $table->date('iqama_expiry_date')->nullable()->after('iqama_photo_path');
            $table->string('iqama_occupation')->nullable()->after('iqama_expiry_date');
            $table->string('current_city')->nullable()->after('iqama_occupation');
            $table->boolean('kafala_transfer_interested')->default(false)->after('current_city');

            $table->index('location_type');
            $table->index('iqama_expiry_date'); // needed later for the 30-day expiry alert widget (Phase 8)
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropIndex(['location_type']);
            $table->dropIndex(['iqama_expiry_date']);

            $table->dropColumn([
                'location_type',
                'iqama_number',
                'iqama_photo_path',
                'iqama_expiry_date',
                'iqama_occupation',
                'current_city',
                'kafala_transfer_interested',
            ]);
        });
    }
};