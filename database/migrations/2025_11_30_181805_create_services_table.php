<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // صاحب الخدمة (البائع)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // التصنيف
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('title', 200);
            $table->string('slug', 200)->unique();

            $table->text('short_description')->nullable();
            $table->longText('description');

            $table->decimal('price', 10, 2);
            $table->unsignedInteger('delivery_time'); // مدة التسليم بالأيام

            $table->enum('level', ['basic', 'standard', 'premium'])
                ->default('basic');

            $table->enum('status', ['active', 'paused', 'draft'])
                ->default('active');

            $table->boolean('allow_messages_before_order')
                ->default(true);

            // التقييمات (تُحدّث عند إضافة review)
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }

};
