<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'discount_type')) {
                $table->string('discount_type', 20)->default('fixed')->after('total');
            }
            if (!Schema::hasColumn('invoice_items', 'discount_value')) {
                $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
            }
            if (!Schema::hasColumn('invoice_items', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_value');
            }
            if (!Schema::hasColumn('invoice_items', 'discount_reason')) {
                $table->string('discount_reason', 255)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('invoice_items', 'discounted_total')) {
                $table->decimal('discounted_total', 12, 2)->default(0)->after('discount_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'discount_amount',
                'discount_reason',
                'discounted_total',
            ]);
        });
    }
};
