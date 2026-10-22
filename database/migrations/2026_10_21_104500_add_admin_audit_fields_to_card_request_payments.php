<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_request_items', function (Blueprint $table) {
            $table->foreignId('paid_by_admin_id')
                ->nullable()
                ->after('paid_by')
                ->constrained('admins')
                ->nullOnDelete();
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->foreignId('recorded_by_admin_id')
                ->nullable()
                ->after('recorded_by')
                ->constrained('admins')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recorded_by_admin_id');
        });

        Schema::table('card_request_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_by_admin_id');
        });
    }
};
