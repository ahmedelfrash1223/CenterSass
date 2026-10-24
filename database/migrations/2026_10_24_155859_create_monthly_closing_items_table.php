<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_closing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_closing_id')->constrained()->cascadeOnDelete();
            $table->string('type');                  // revenue | expense
            $table->unsignedBigInteger('source_id'); // ID of revenue/expense row
            $table->string('reference')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('source_type')->nullable(); // enum value from RevenueSourceEnum / ExpenseSourceEnum
            $table->string('description', 500)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['monthly_closing_id', 'type']);
            $table->index(['type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_closing_items');
    }
};
