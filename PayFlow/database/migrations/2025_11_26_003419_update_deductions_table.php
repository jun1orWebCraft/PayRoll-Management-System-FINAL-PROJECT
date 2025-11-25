<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deductions', function (Blueprint $table) {
            // Add employee_id if it doesn't exist
            if (!Schema::hasColumn('deductions', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->after('id');
                $table->foreign('employee_id')
                      ->references('employee_id')
                      ->on('employees')
                      ->cascadeOnDelete();
            }

            // Set deduction_date default to current date
            $table->date('deduction_date')->useCurrent()->change();
        });
    }

    public function down(): void
    {
        Schema::table('deductions', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('deductions', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }

            // Optionally revert deduction_date change if needed
        });
    }
};
