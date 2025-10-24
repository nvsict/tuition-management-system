<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->foreignId('batch_id')
              ->nullable()
              ->after('class') // Places it after the 'class' column
              ->constrained()
              ->onDelete('set null'); // If a batch is deleted, set student's batch_id to NULL
    });
}

    public function down(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropForeign(['batch_id']);
        $table->dropColumn('batch_id');
    });
}
};
