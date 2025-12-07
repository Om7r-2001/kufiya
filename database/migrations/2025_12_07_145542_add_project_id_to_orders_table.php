<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // حقل اختياري يربط الطلب بمشروع (بدون FK لتفادي مشاكل القيود)
            $table->unsignedBigInteger('project_id')
                  ->nullable()
                  ->after('service_id') // عدّل مكانه لو ما عندك service_id
                  ->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });
    }
};