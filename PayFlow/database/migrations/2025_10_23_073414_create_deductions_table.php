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
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->decimal('sss', 10, 2)->default(0);          
            $table->decimal('philhealth', 10, 2)->default(0); 
            $table->decimal('pagibig', 10, 2)->default(0); 
            $table->decimal('withholding_tax', 10, 2)->default(0); 
            $table->decimal('total_deduction', 10, 2)->default(0); 
            $table->date('deduction_date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
