<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->longText('QR_code')->unique()->nullable();
            $table->string('employee_no', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('phone', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('hire_date')->nullable();
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Inactive');
            $table->enum('employment_type', ['Full-Time', 'Part-Time'])->default('Full-Time');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->foreign('position_id')
                  ->references('position_id')
                  ->on('positions')
                  ->nullOnDelete();
            $table->string('profile_picture')->nullable();
            $table->rememberToken(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
