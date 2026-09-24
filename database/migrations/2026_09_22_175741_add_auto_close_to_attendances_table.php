<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'is_auto_closed')) {
                $table->boolean('is_auto_closed')->default(false)->after('status');
            }
            if (!Schema::hasColumn('attendances', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('is_auto_closed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['is_auto_closed', 'closed_at']);
        });
    }
};
