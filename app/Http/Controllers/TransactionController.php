<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display the main fee ledger page.
     */
    public function index(Request $request)
    {
        // 1. Master Student List for Dropdown (Always Needed)
        $masterStudentList = Student::orderBy('name')->with('batch')->get();
        
        // 2. Force Charge Generation (Ensures Balances are Accurate)
        // Still run this before loading any student's ledger
        foreach ($masterStudentList as $student) {
            $this->autoGenerateCharges($student);
        }

        // 3. Determine Selected Student (Simpler logic)
        $selectedStudent = null;
        if ($request->has('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        // 4. Final Ledger Setup (Fetch balance if student selected)
        $transactions = collect();
        $currentBalance = 0;
        if ($selectedStudent) {
            // Fetch balance directly for the selected student after charges generated
             $selectedStudentData = DB::table('students')
                ->selectRaw('SUM(COALESCE(transactions.amount, 0)) as total_due_amount')
                ->leftJoin('transactions', 'students.id', '=', 'transactions.student_id')
                ->where('students.id', '=', $selectedStudent->id)
                ->groupBy('students.id')
                ->first();
            $currentBalance = $selectedStudentData->total_due_amount ?? 0;
            $transactions = $selectedStudent->transactions()->get();
            // Attach the balance for view consistency
            $selectedStudent->transactions_sum_amount = $currentBalance; 
        }

        // 5. Calculate total due (Overall stat for the top card)
        $totalDue = $this->calculateTotalDue();

        return view('transactions.index', compact(
            'masterStudentList', 
            'selectedStudent',
            'transactions',
            'currentBalance',
            'totalDue'
            // 'isDueSoonFilter' and 'dueSoonStudents' are removed
        ));
    }
    
    /**
     * Store a new PAYMENT transaction.
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

        return redirect()->route('fees.index', ['student_id' => $validated['student_id']])
                         ->with('success', 'Payment recorded successfully.');
    }


    /**
     * Update an existing transaction (handles PUT request from the modal).
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01', // Absolute value from form
            'date' => 'required|date',
        ]);
        
        $newAmount = $validated['amount'];
        if ($transaction->amount < 0) { // Keep the sign if it was a payment
            $newAmount = -$newAmount;
        }
        
        $transaction->update([
            'description' => $validated['description'],
            'amount' => $newAmount,
            'date' => $validated['date'],
        ]);
        
        return redirect()->route('fees.index', ['student_id' => $transaction->student_id])
                         ->with('success', 'Transaction updated successfully.');
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
    
    /**
     * Display a list of students with outstanding fee balances (Reminder List page).
     */
    public function reminders()
    {
        // Force charge generation before querying for dues
        $allStudentsForCharges = Student::whereNotNull('join_date')->get();
        foreach ($allStudentsForCharges as $student) {
             $this->autoGenerateCharges($student);
        }
        
        // Now query students with a positive balance
        $studentsWithDues = Student::with('batch') // Eager load batch for the view
                                   ->withSum('transactions', 'amount')
                                   ->having('transactions_sum_amount', '>', 0)
                                   ->orderBy('name')
                                   ->get();

        return view('transactions.reminders', compact('studentsWithDues'));
    }

    // =========================================================================
    //  PRIVATE HELPER FUNCTIONS
    // =========================================================================

    /**
     * Auto-creates any missed monthly fee "CHARGES".
     * KEPT: Essential for accurate balances.
     */
     private function autoGenerateCharges(Student $student)
     {
         $joinDate = $student->join_date;
         $monthlyFee = $student->fees;

         if (!$joinDate || $monthlyFee <= 0) {
             return;
         }

         $lastCharge = $student->transactions()
                               ->where('amount', '>', 0)
                               ->orderBy('date', 'desc')
                               ->first();

         $cursorDate = $lastCharge ? Carbon::parse($lastCharge->date) : Carbon::parse($joinDate);
         $billingDay = Carbon::parse($joinDate)->day; 

         $nextBillingDate = $cursorDate->copy()->day($billingDay);
         if ($nextBillingDate->lte($cursorDate)) {
              $nextBillingDate->addMonthNoOverflow();
         }
         $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));

         while ($nextBillingDate->isPast() || $nextBillingDate->isToday()) {
             $periodStartDate = $nextBillingDate->copy()->subMonthNoOverflow();
             $periodStartDate->day(min($billingDay, $periodStartDate->daysInMonth)); 
             $periodEndDate = $nextBillingDate->copy()->subDay();

             $chargeExists = $student->transactions()
                                     ->where('amount', '>', 0)
                                     ->where('date', '>=', $periodStartDate->toDateString())
                                     ->where('date', '<', $nextBillingDate->toDateString())
                                     ->exists();

             if (!$chargeExists) {
                  Transaction::create([
                     'student_id' => $student->id,
                     'description' => 'Fee for ' . $periodStartDate->format('M d') . ' - ' . $periodEndDate->format('M d, Y'),
                     'amount' => $monthlyFee,
                     'date' => $periodStartDate->toDateString(), 
                 ]);
             }
             $nextBillingDate->addMonthNoOverflow();
             $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));
         }
     }

    /**
     * Calculates the total amount due from all students (Overall stat).
     * KEPT: Used in index method.
     */
    private function calculateTotalDue()
    {
        // Ensure charges are generated before calculating total due across all students
         $allStudents = Student::whereNotNull('join_date')->get();
         foreach ($allStudents as $student) {
             $this->autoGenerateCharges($student);
         }
         // Now sum the transactions
        return Transaction::sum('amount');
    }

    // --- getStudentsDueSoonList() function REMOVED ---
}