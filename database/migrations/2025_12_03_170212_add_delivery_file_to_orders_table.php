<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_file')->nullable()->after('status');
            $table->text('delivery_note')->nullable()->after('delivery_file');
            $table->timestamp('delivered_at')->nullable()->after('delivery_note');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_file', 'delivery_note', 'delivered_at']);
        });
    }
};
