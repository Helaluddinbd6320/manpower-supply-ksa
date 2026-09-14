<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // যেমন: হাউসমেইড, ওয়েটার/ওয়েট্রেস
            $table->string('name_en')->nullable();   // ইংরেজি নাম (ভবিষ্যতে ইংরেজি CV/রিপোর্টে ব্যবহারের জন্য)
            $table->string('slug')->unique();
            $table->string('group');                 // যেমন: Household/Domestic, Hospitality/Food, ইত্যাদি
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('group');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_categories');
    }
};