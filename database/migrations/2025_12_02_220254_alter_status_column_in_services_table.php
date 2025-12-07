<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE services MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // في حالة الرجوع للخلف يمكنك إرجاعه لما كان عليه (عدّل هذه حسب تعريفك القديم)
        DB::statement("ALTER TABLE services MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }
};
