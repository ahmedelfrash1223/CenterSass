<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->decimal('price', 15, 2);
            $table->unsignedTinyInteger('discount_type')->comment('1 = PERCENTAGE, 2 = VALUE');
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('price_after_discount', 15, 2);
            $table->unsignedInteger('duration');
            $table->unsignedTinyInteger('duration_type')->comment('1 = DAY, 2 = WEEK, 3 = MONTH, 4 = YEAR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
