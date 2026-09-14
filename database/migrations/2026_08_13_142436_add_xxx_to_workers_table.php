<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // adjust "after" if your last column from marital_status/location_type migration differs
            $table->unsignedSmallInteger('experience_years')->nullable()->after('marital_status');
            $table->string('driving_license_type')->default('none')->after('experience_years');
            $table->string('arabic_proficiency')->default('none')->after('driving_license_type');
            $table->string('english_proficiency')->default('none')->after('arabic_proficiency');
            $table->string('education_qualification')->nullable()->after('english_proficiency');
            $table->string('trade_test_certificate')->nullable()->after('education_qualification');
            $table->text('experience_description')->nullable()->after('trade_test_certificate');
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years',
                'driving_license_type',
                'arabic_proficiency',
                'english_proficiency',
                'education_qualification',
                'trade_test_certificate',
                'experience_description',
            ]);
        });
    }
};