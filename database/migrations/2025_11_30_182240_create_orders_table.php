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

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('seller_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('price', 10, 2);

            $table->enum('status', [
                'pending',
                'in_progress',
                'delivered',
                'completed',
                'cancelled',
                'disputed'
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->date('delivery_date')->nullable();
            $table->datetime('completed_at')->nullable();

            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }

};
