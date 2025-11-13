<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('sss', 10, 2)->nullable();          
            $table->decimal('philhealth', 10, 2)->nullable();
            $table->decimal('pagibig', 10, 2)->nullable();
            $table->decimal('withholding_tax', 10, 2)->nullable();
            $table->decimal('total_deduction', 10, 2)->default(0);
            $table->date('deduction_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
