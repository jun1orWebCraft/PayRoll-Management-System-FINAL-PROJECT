<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // reference to the employee
            $table->string('type', 50);               // e.g., 'leave_approved', 'leave_rejected'
            $table->text('message');                  // notification message
            $table->string('link')->nullable();       // optional link to details
            $table->boolean('is_read')->default(false); // 0 = unread, 1 = read
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
