<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // السعر النهائي للطلب (يمكن = price الخدمة أو مع إضافات)
            $table->decimal('total_price', 10, 2)->nullable();

            // حالة الدفع: pending, paid, released, refunded
            $table->string('payment_status')->default('pending');

            // طريقة الدفع الوهمية (بطاقة، محفظة، إلخ)
            $table->string('payment_method')->nullable();

            // عمولة المنصة (20%)
            $table->decimal('platform_fee', 10, 2)->default(0);

            // نصيب مزود الخدمة من هذا الطلب
            $table->decimal('seller_earnings', 10, 2)->default(0);

            // تواريخ مهمة
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('released_at')->nullable(); // متى حُرّر المبلغ للبائع
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_price',
                'payment_status',
                'payment_method',
                'platform_fee',
                'seller_earnings',
                'paid_at',
                'released_at',
            ]);
        });
    }

};
