<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->enum('status', ['Pending', 'Processed', 'Paid'])->default('Pending');

            // Foreign key linking to employees
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
