<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Teacher
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Package
            $table->foreignId('package_id')
                ->constrained('packages')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Subscription start date
            $table->dateTime('starts_at');

            // Subscription end date
            $table->dateTime('ends_at');

            // Price at the time of subscription
            $table->decimal('price', 15, 2);

            // Discount type at the time of subscription
            $table->unsignedTinyInteger('discount_type')
                ->nullable()
                ->comment('1 = PERCENTAGE, 2 = VALUE');

            // Discount value at the time of subscription
            $table->decimal('discount_value', 15, 2)
                ->default(0);

            // Final price after discount
            $table->decimal('price_after_discount', 15, 2);

            // Subscription status
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('1 = ACTIVE, 2 = EXPIRED, 3 = CANCELLED, 4 = PENDING');

            // Payment status
            $table->boolean('is_paid')
                ->default(false);

            // Payment date
            $table->dateTime('paid_at')
                ->nullable();

            // Previous subscription
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('subscriptions')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();

            // Indexes
            $table->index(['teacher_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
