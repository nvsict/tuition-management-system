<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\Batch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- PRE-CALCULATION STEP: Ensure all charges are generated ---
        // Run this BEFORE calculating balances/overdue counts
        $allStudentsForCharges = Student::whereNotNull('join_date')->get();
        // **IMPORTANT**: The autoGenerateCharges method below is required for this.
        foreach ($allStudentsForCharges as $student) {
             $this->autoGenerateCharges($student);
        }
        // --- END PRE-CALCULATION ---

        // 1. Total Students
        $totalStudents = Student::count();

        // 2. Students Overdue (Balance > 0)
        $studentsOverdueCount = DB::table('students')
            ->selectRaw('SUM(COALESCE(transactions.amount, 0)) as total_due_amount')
            ->leftJoin('transactions', 'students.id', '=', 'transactions.student_id')
            ->groupBy('students.id')
            ->havingRaw('SUM(COALESCE(transactions.amount, 0)) > 0')
            ->count();

        // --- UPCOMING DUES FUNCTIONALITY COMPLETELY REMOVED ---

        // --- Calculate Batch Payment Health ---
        $batches = Batch::select('batches.id', 'batches.name')
            ->selectRaw('COUNT(students.id) AS total_students')
            ->leftJoin('students', 'batches.id', '=', 'students.batch_id')
            ->groupBy('batches.id', 'batches.name')
            ->orderBy('batches.name')
            ->get()->keyBy('id');

        $overdueCounts = DB::table('students')
            ->select('batch_id', DB::raw('COUNT(students.id) as overdue_count'))
            ->leftJoin('transactions', 'students.id', '=', 'transactions.student_id')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id')
            ->havingRaw('SUM(COALESCE(transactions.amount, 0)) > 0')
            ->pluck('overdue_count', 'batch_id');

        $batchHealth = $batches->map(function($batch) use ($overdueCounts) {
            $overdueCount = $overdueCounts->get($batch->id, 0);
            $batch->overdue_students = $overdueCount;
            $batch->overdue_percent = ($batch->total_students > 0)
                ? round(($overdueCount / $batch->total_students) * 100, 1) : 0;
            return $batch;
        });
        // --- End Batch Payment Health ---

        // 3. Fees Collected / Due (Overall)
        $totalCollected = Transaction::where('amount', '<', 0)->sum('amount') * -1;
        $totalDue = Transaction::sum('amount'); // Overall balance after charges generated

        // 4. Today's Attendance
        $today = Carbon::today();
        $totalPresentToday = Attendance::where('date', $today->toDateString())->where('status', 'present')->count();
        $totalAbsentToday = Attendance::where('date', $today->toDateString())->where('status', 'absent')->count();

        // 5. Monthly Growth Chart
         $growthData = Student::select(
                DB::raw('YEAR(join_date) as year'),
                DB::raw('MONTH(join_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('join_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')->orderBy('month', 'asc')
            ->get();
        $chartLabels = []; $chartData = [];
        $currentDate = Carbon::now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthLabel = $currentDate->format('M Y'); $chartLabels[] = $monthLabel;
            $found = $growthData->first(fn($item) => $item->year == $currentDate->year && $item->month == $currentDate->month);
            $chartData[] = $found ? $found->count : 0;
            $currentDate->addMonth();
        }

        return view('dashboard', compact(
            'totalStudents',
            'studentsOverdueCount',
            // 'upcomingDuesCount', // REMOVED
            'totalCollected',
            'totalDue',
            'totalPresentToday',
            'totalAbsentToday',
            'chartLabels',
            'chartData',
            'batchHealth'
        ));
    }

    // =========================================================================
    //  PRIVATE HELPER FUNCTIONS
    // =========================================================================

    /**
     * Auto-creates any missed monthly fee "CHARGES".
     * KEPT: This is still needed to ensure accurate balances before counting overdue.
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

         // Use join date directly if no charges exist, otherwise use last charge date
         $cursorDate = $lastCharge ? Carbon::parse($lastCharge->date) : Carbon::parse($joinDate);
         $billingDay = Carbon::parse($joinDate)->day; // Ensure billing day comes from Carbon instance

         $nextBillingDate = $cursorDate->copy()->day($billingDay);
         if ($nextBillingDate->lte($cursorDate)) {
              $nextBillingDate->addMonthNoOverflow();
         }
         // Ensure day is correctly set, handling month length differences
         $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));


         while ($nextBillingDate->isPast() || $nextBillingDate->isToday()) {
             // Calculate period start date carefully
             $periodStartDate = $nextBillingDate->copy()->subMonthNoOverflow();
             $periodStartDate->day(min($billingDay, $periodStartDate->daysInMonth)); // Set day for start date

             $periodEndDate = $nextBillingDate->copy()->subDay();

             $chargeExists = $student->transactions()
                                     ->where('amount', '>', 0)
                                     // Check if a charge exists ON or AFTER the period start date
                                     // AND BEFORE the next billing date.
                                     ->where('date', '>=', $periodStartDate->toDateString())
                                     ->where('date', '<', $nextBillingDate->toDateString())
                                     ->exists();

             if (!$chargeExists) {
                  Transaction::create([
                     'student_id' => $student->id,
                     'description' => 'Fee for ' . $periodStartDate->format('M d') . ' - ' . $periodEndDate->format('M d, Y'),
                     'amount' => $monthlyFee,
                     'date' => $periodStartDate->toDateString(), // Date the charge to the start of the period
                 ]);
             }
             // Move to the next billing date
             $nextBillingDate->addMonthNoOverflow();
             $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));
         }
     }

     // --- getStudentsDueSoonList() function REMOVED ---
}