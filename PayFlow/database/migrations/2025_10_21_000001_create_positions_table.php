<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id('position_id'); // Primary key
            $table->string('position_name', 100);
            $table->decimal('salary_rate', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
