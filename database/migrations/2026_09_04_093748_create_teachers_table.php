<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->uuid('uuid')->unique();
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable()->unique();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            $table->unsignedTinyInteger('role')
                ->default(1)
                ->comment('1 = Owner (مسئول), 2 = Staff (مدرس), 3 = Assistant (مساعد)');

            $table->integer('is_active')->default(1);
            $table->timestamps();

            $table->index(['owner_id', 'role']);
            $table->index(['owner_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
