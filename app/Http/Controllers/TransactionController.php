<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display the main fee ledger page.
     * This page will show a student's balance and transaction history.
     */
    public function index(Request $request)
    {
        $students = Student::orderBy('name')->get();
        $selectedStudent = null;
        $transactions = [];
        $currentBalance = 0;

        // Check if a specific student is being viewed
        if ($request->has('student_id')) {
            $selectedStudent = Student::find($request->student_id);

            if ($selectedStudent) {
                // This is the magic! Auto-create any missed fee charges
                // before we display the ledger.
                $this->autoGenerateCharges($selectedStudent);

                // Now, fetch all transactions for this student
                $transactions = $selectedStudent->transactions()->get();
                
                // Calculate their true balance
                $currentBalance = $transactions->sum('amount');
            }
        }

        // Calculate total due from all students
        $totalDue = $this->calculateTotalDue();

        return view('transactions.index', compact(
            'students',
            'selectedStudent',
            'transactions',
            'currentBalance',
            'totalDue'
        ));
    }

    /**
     * Store a new PAYMENT transaction.
     * This form is for receiving money from the student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        Transaction::create([
            'student_id' => $validated['student_id'],
            'description' => $validated['description'] ?? 'Payment',
            'amount' => -$validated['amount_paid'], // Store as a negative value
            'date' => $validated['date'],
        ]);

        // Redirect back to the same student's ledger
        return redirect()->route('fees.index', ['student_id' => $validated['student_id']])
                         ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Remove a transaction (e.g., a mistaken payment or charge).
     */
    public function destroy(Transaction $transaction)
    {
        $studentId = $transaction->student_id;
        $transaction->delete();

        return redirect()->route('fees.index', ['student_id' => $studentId])
                         ->with('success', 'Transaction deleted successfully.');
    }

    // =========================================================================
    //  PRIVATE HELPER FUNCTIONS
    // =========================================================================

    /**
     * This is the core logic of the billing system.
     * It checks a student's join date and last charge, and creates any
     * missed monthly fee "CHARGES" to bring their account up to date.
     */
    private function autoGenerateCharges(Student $student)
{
    $joinDate = $student->join_date;
    $monthlyFee = $student->fees;

    // Skip if join date is not set or fee is zero
    if (!$joinDate || $monthlyFee <= 0) {
        return;
    }

    // Find the date of the last CHARGE (invoice)
    $lastCharge = $student->transactions()
                          ->where('amount', '>', 0) // 'amount > 0' means it's a CHARGE
                          ->orderBy('date', 'desc')
                          ->first();

    // Determine the starting point for checking
    // If no charges exist, start checking from the join date.
    // If charges exist, start checking from the date of the last charge.
    $cursorDate = $lastCharge ? Carbon::parse($lastCharge->date) : Carbon::parse($joinDate);

    // Get the "billing day" (e.g., 25th)
    $billingDay = $joinDate->day;

    // Calculate the next theoretical billing date after the cursor date
    // Create a mutable copy to avoid changing $cursorDate
    $nextBillingDate = $cursorDate->copy()->day($billingDay); 
    // If the calculated day is before or the same as the cursor, move to the next month
    if ($nextBillingDate->lte($cursorDate)) {
         $nextBillingDate->addMonthNoOverflow(); // Use NoOverflow to handle end-of-month cases
    }

    // Ensure the billing day is respected if months have different lengths
    $nextBillingDate->day($billingDay); 


    // Loop from the next billing date until today
    // This loop "catches up" all missed billing cycles
    while ($nextBillingDate->isPast() || $nextBillingDate->isToday()) {

        // Define the billing period this charge represents
        $periodStartDate = $nextBillingDate->copy()->subMonthNoOverflow()->day($billingDay);
        $periodEndDate = $nextBillingDate->copy()->subDay(); // Day before the current billing date

        // MORE ROBUST CHECK: Does a charge already exist *within* this billing period?
        $chargeExists = $student->transactions()
                                ->where('amount', '>', 0)
                                ->whereBetween('date', [$periodStartDate->toDateString(), $periodEndDate->toDateString()])
                                ->exists();

        if (!$chargeExists) {
            // Create the new CHARGE transaction, dated at the START of the billing period
             Transaction::create([
                'student_id' => $student->id,
                'description' => 'Fee for ' . $periodStartDate->format('M d') . ' - ' . $periodEndDate->format('M d, Y'),
                'amount' => $monthlyFee, // Positive value
                'date' => $periodStartDate->toDateString(), // Use the period start date
            ]);
        }

        // Move to the next month's billing date to check again
        $nextBillingDate->addMonthNoOverflow()->day($billingDay);
    }
}

    /**
     * Calculates the total amount due from all students.
     * This is an expensive query, so it should be used sparingly.
     */
    private function calculateTotalDue()
    {
        // This sums the 'amount' column for ALL transactions.
        // Since payments are negative, this gives us the true balance.
        return Transaction::sum('amount');
    }

    /**
 * Display a list of students with outstanding fee balances.
 */
public function reminders()
{
    // Query students, calculate the sum of their transactions,
    // and filter for those where the sum (balance) is greater than 0.
    $studentsWithDues = Student::withSum('transactions', 'amount') // This creates 'transactions_sum_amount'
                               ->having('transactions_sum_amount', '>', 0)
                               ->orderBy('name')
                               ->get();

    return view('transactions.reminders', compact('studentsWithDues'));
}
}