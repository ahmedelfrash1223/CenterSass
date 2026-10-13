<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_requests', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->after('cards_count');
            $table->decimal('total_price', 10, 2)->default(0)->after('unit_price');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_price');
            $table->unsignedInteger('paid_cards_count')->default(0)->after('paid_amount');
        });

        Schema::table('card_request_items', function (Blueprint $table) {
            $table->decimal('card_price', 10, 2)->default(0)->after('group_id');
            $table->boolean('is_paid')->default(false)->after('card_price');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
            $table->foreignId('paid_by')->nullable()->after('paid_at')
                ->constrained('teachers')->nullOnDelete();
            $table->unsignedBigInteger('revenue_id')->nullable()->after('paid_by');
        });
    }

    public function down(): void
    {
        Schema::table('card_request_items', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn(['card_price', 'is_paid', 'paid_at', 'paid_by', 'revenue_id']);
        });

        Schema::table('card_requests', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_price', 'paid_amount', 'paid_cards_count']);
        });
    }
};
