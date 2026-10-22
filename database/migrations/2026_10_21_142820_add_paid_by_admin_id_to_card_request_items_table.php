<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_request_items', function (Blueprint $table) {
            if (! Schema::hasColumn('card_request_items', 'paid_by_admin_id')) {
                $table->foreignId('paid_by_admin_id')->nullable()->after('paid_by')
                    ->constrained('admins')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('card_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('card_request_items', 'paid_by_admin_id')) {
                $table->dropConstrainedForeignId('paid_by_admin_id');
            }
        });
    }
};
