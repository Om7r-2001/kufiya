<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_file')) {
                $table->string('delivery_file')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'delivery_note')) {
                $table->text('delivery_note')->nullable()->after('delivery_file');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('delivery_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = array_filter(
                ['delivery_file', 'delivery_note', 'delivered_at'],
                fn ($col) => Schema::hasColumn('orders', $col)
            );
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
