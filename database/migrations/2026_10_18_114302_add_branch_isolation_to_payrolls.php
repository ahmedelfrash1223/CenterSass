<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_payrolls', function (Blueprint $table) {
            $table->boolean('is_reviewed')->default(false)->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('is_reviewed');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')
                ->constrained('teachers')->nullOnDelete();

            $table->text('review_notes')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_payrolls', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['is_reviewed', 'reviewed_at', 'reviewed_by', 'review_notes']);
        });
    }
};
