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
        Schema::table('students', function (Blueprint $table) {
            // Drop the unique index on the 'phone' column
            // Note: The index name may vary based on your Laravel version, 
            // but this is the standard naming convention.
            $table->dropUnique('students_phone_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add the unique index back if the migration is rolled back
            $table->unique('phone');
        });
    }
};