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
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('current_subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->foreignId('requested_package_id')->constrained('packages')->cascadeOnDelete();
            $table->unsignedTinyInteger('status')->default(1)->comment('1: PENDING, 2: APPROVED, 3: REJECTED, 4: CANCELLED');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('teacher_id');
            $table->index('status');
            $table->index('created_at');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_requests');
    }
};
