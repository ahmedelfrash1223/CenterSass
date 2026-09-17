<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('pricing_type', 30)
                ->default('fixed_monthly')
                ->after('status');

            $table->decimal('pricing_amount', 10, 2)
                ->default(0)
                ->after('pricing_type');

            $table->json('pricing_config')
                ->nullable()
                ->after('pricing_amount');

            $table->boolean('has_pricing')
                ->default(true)
                ->after('pricing_config');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_type',
                'pricing_amount',
                'pricing_config',
                'has_pricing',
            ]);
        });
    }
};
