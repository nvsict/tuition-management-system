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
        Schema::table('transactions', function (Blueprint $table) {
            // 1. Add new columns for the ledger

            // This will hold "Fee for Oct-Nov" or "Payment"
            $table->string('description')->after('student_id');

            // This will hold positive (charges) and negative (payments)
            // e.g., +1000.00 or -500.00
            $table->decimal('amount', 8, 2)->after('description');

            // 2. Drop the old columns
            $table->dropColumn('month');
            $table->dropColumn('amount_paid');
            $table->dropColumn('due_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // 1. Add back the old columns
            $table->string('month')->after('student_id');
            $table->decimal('amount_paid', 8, 2);
            $table->decimal('due_amount', 8, 2)->default(0);

            // 2. Drop the new columns
            $table->dropColumn('description');
            $table->dropColumn('amount');
        });
    }
};