<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       
        Schema::create('deduction_types', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('type_name'); // Tax, SSS, PhilHealth, etc.
            $table->decimal('default_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // 2️⃣ Then create deductions referencing type_id
        Schema::create('deductions', function (Blueprint $table) {
            $table->id('deduction_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('type_id')->nullable(); // link to deduction_types

            $table->string('deduction_name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('deduction_date')->nullable();
            $table->text('remarks')->nullable();

            // Foreign keys
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->cascadeOnDelete();

            $table->foreign('type_id')
                  ->references('type_id')
                  ->on('deduction_types')
                  ->nullOnDelete(); // null if type deleted

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
        Schema::dropIfExists('deduction_types');
    }
};
