<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_bids', function (Blueprint $table) {
            $table->id();

            // نستخدم أرقام فقط، بدون قيود FK لتفادي الخطأ
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('user_id')->index();

            $table->unsignedInteger('amount');
            $table->unsignedInteger('delivery_days')->nullable();
            $table->text('message')->nullable();

            $table->enum('status', [
                'pending',   // قيد المراجعة
                'accepted',  // تم قبول العرض
                'rejected',  // مرفوض
            ])->default('pending');

            $table->timestamps();

            // منع تكرار عرض لنفس المشروع من نفس المزود (اختياري)
            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_bids');
    }
};

