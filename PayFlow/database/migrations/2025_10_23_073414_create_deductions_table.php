<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id('deduction_id');
            $table->unsignedBigInteger('employee_id'); // link to employee
            $table->string('deduction_name'); // e.g., Tax, SSS, Loan
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('deduction_date')->nullable();
            $table->text('remarks')->nullable(); // optional notes

            // Foreign key
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
