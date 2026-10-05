<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('grade')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('max_students')->default(0);
            $table->string('status', 20)->default('active');
            $table->string('pricing_type', 30)->default('fixed_monthly');
            $table->decimal('pricing_amount', 10, 2)->default(0);
            $table->json('pricing_config')->nullable();
            $table->boolean('has_pricing')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'name']);
            $table->index(['teacher_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
