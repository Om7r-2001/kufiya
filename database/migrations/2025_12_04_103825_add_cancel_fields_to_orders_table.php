<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        // إذا لم يوجد العمود cancel_reason أضِفه، غير ذلك تجاهل
        if (!Schema::hasColumn('orders', 'cancel_reason')) {
            $table->text('cancel_reason')->nullable()->after('status');
        }

        // إذا لم يوجد العمود cancelled_at أضِفه، غير ذلك تجاهل
        if (!Schema::hasColumn('orders', 'cancelled_at')) {
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
        }
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        if (Schema::hasColumn('orders', 'cancel_reason')) {
            $table->dropColumn('cancel_reason');
        }
    });
}


};