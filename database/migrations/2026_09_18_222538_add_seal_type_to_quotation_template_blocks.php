<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quotation_template_blocks MODIFY COLUMN type ENUM('header_image', 'client_info', 'terms', 'signature', 'footer', 'seal') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quotation_template_blocks MODIFY COLUMN type ENUM('header_image', 'client_info', 'terms', 'signature', 'footer') NOT NULL");
    }
};