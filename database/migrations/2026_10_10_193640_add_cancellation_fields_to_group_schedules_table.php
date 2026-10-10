
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_schedules', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false);
            $table->string('cancellation_reason')->nullable();
            $table->text('cancellation_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();

            $table->index(['group_id', 'day', 'is_cancelled']);
        });
    }

    public function down(): void
    {
        Schema::table('group_schedules', function (Blueprint $table) {
            $table->dropIndex(['group_id', 'day', 'is_cancelled']);
            $table->dropColumn([
                'is_cancelled',
                'cancellation_reason',
                'cancellation_notes',
                'cancelled_at',
                'cancelled_by',
            ]);
        });
    }
};
