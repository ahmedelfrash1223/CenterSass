<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            $table->string('discount_type', 20)->default('fixed')->after('subtotal');

            $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');

            $table->string('discount_reason', 255)->nullable()->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'discount',
                'discount_reason',
            ]);
        });
    }
};
