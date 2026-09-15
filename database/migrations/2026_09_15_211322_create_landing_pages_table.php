<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            // SEO ফিল্ড
            $table->string('meta_title');
            $table->string('meta_description', 500);
            $table->string('h1_heading');

            // কনটেন্ট
            $table->longText('intro_content');
            $table->json('highlights')->nullable(); // বুলেট পয়েন্ট লিস্ট (যেমন: "Fast Deployment", "Valid Iqama")

            // ক্যাটাগরাইজেশন (রিপোর্টিং ও ফিল্টারের জন্য, ভবিষ্যতে কাজে লাগবে)
            $table->string('city_name')->nullable();
            $table->string('job_category_name')->nullable();
            $table->enum('page_type', ['city', 'category', 'city_category', 'custom'])
                ->default('custom');

            // পাবলিশ কন্ট্রোল
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // অডিট ট্রেইল (প্রজেক্টের বিদ্যমান প্যাটার্ন অনুযায়ী)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['is_published', 'page_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};