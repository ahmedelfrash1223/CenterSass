<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_closings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('closed_by')->constrained('teachers');
            $table->string('month', 7);              // YYYY-MM
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('total_expense', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->unsignedInteger('revenues_count')->default(0);
            $table->unsignedInteger('expenses_count')->default(0);
            $table->enum('status', ['draft', 'closed'])->default('closed');
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['teacher_id', 'month']);
            $table->index(['teacher_id', 'branch_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_closings');
    }
};
