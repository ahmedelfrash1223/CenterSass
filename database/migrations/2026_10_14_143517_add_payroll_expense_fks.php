<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('teacher_payroll_id')
                ->references('id')
                ->on('teacher_payrolls')
                ->nullOnDelete();
        });

        Schema::table('teacher_payroll_bonuses', function (Blueprint $table) {
            $table->foreign('expense_id')
                ->references('id')
                ->on('expenses')
                ->nullOnDelete();
        });

        Schema::table('teacher_payroll_payments', function (Blueprint $table) {
            $table->foreign('expense_id')
                ->references('id')
                ->on('expenses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teacher_payroll_payments', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
        });

        Schema::table('teacher_payroll_bonuses', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['teacher_payroll_id']);
        });
    }
};
