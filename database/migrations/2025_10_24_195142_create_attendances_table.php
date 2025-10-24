<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('attendance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->enum('status', ['present', 'absent']);
        $table->timestamps();

        // IMPORTANT: A student can only have one entry per day.
        $table->unique(['student_id', 'date']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
