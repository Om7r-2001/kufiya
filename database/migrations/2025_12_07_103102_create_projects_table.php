<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            // صاحب المشروع (المشتري)
            $table->unsignedBigInteger('user_id');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();

            $table->unsignedInteger('budget_min')->nullable();
            $table->unsignedInteger('budget_max')->nullable();

            $table->unsignedInteger('delivery_days')->nullable();

            $table->text('description');

            $table->enum('status', [
                'open',
                'in_progress',
                'completed',
                'cancelled',
                'expired',
            ])->default('open');

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('bids_count')->default(0);

            $table->unsignedBigInteger('selected_bid_id')->nullable()->index();

            $table->timestamps();

            // FK على users
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

