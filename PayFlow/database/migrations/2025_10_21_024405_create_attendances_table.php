<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable(); // total working hours per day
            $table->enum('status', ['Present', 'Absent', 'Late', 'On Leave'])->default('Absent');
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
