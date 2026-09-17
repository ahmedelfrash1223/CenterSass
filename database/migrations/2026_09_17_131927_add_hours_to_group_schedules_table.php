<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('group_schedules', function (Blueprint $table) {
            // Duration in hours (e.g. 1.5, 2.0, 0.5)
            $table->decimal('hours', 3, 1)
                ->default(1.0)
                ->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('group_schedules', function (Blueprint $table) {
            $table->dropColumn('hours');
        });
    }
};
