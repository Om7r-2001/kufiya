<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // صاحب الإشعار

            $table->string('type')->nullable();   // نوع الإشعار (order, message, service...)
            $table->string('title');              // عنوان مختصر
            $table->text('body')->nullable();     // نص الإشعار
            $table->string('link')->nullable();   // رابط عند الضغط على الإشعار
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};