<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Transaction; // <-- CHANGED FROM Fee to Transaction
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // 1. Total Students
    $totalStudents = Student::count();

    // 2. Students Overdue (Balance > 0)
    $studentsOverdueCount = Student::withSum('transactions', 'amount')
                                   ->having('transactions_sum_amount', '>', 0)
                                   ->count();

    // --- Calculate Upcoming Dues (Next 7 Days) ---
    $upcomingDuesCount = 0;
    $today = Carbon::today();
    $oneWeekLater = Carbon::today()->addDays(7);

    // Get all active students with join dates and their last charge date
    $studentsToCheck = Student::whereNotNull('join_date')
        ->withSum('transactions', 'amount') // Get current balance
        ->withMax('transactions as last_charge_date', 'date', function ($query) {
             $query->where('amount', '>', 0); // Only consider charges
         })
        ->get();

    foreach ($studentsToCheck as $student) {
        $joinDate = Carbon::parse($student->join_date);
        $billingDay = $joinDate->day;

        // Determine the date from which to calculate the next billing cycle
        // Use last charge date if available, otherwise use join date
        $cursorDate = $student->last_charge_date ? Carbon::parse($student->last_charge_date) : $joinDate->copy();

        // Calculate the next theoretical billing date after the cursor date
        $nextBillingDate = $cursorDate->copy()->day($billingDay);
        if ($nextBillingDate->lte($cursorDate)) {
             $nextBillingDate->addMonthNoOverflow();
        }
        $nextBillingDate->day($billingDay); // Re-apply billing day


        // Check if this calculated date falls within the next 7 days
        if ($nextBillingDate->between($today, $oneWeekLater)) {
            
            // Refined Check: Has the student paid *enough* since their last charge
            // to cover this upcoming cycle?
            
            // Get the date the last cycle *started* (which is the date of the last charge)
            $lastCycleStartDate = $student->last_charge_date ? Carbon::parse($student->last_charge_date) : $joinDate->copy()->subMonthNoOverflow(); // Estimate if no charges yet
            
            // Sum payments made *since* the beginning of the last cycle
            $paymentsSinceLastCycle = Transaction::where('student_id', $student->id)
                                                 ->where('amount', '<', 0) // Only payments
                                                 ->where('date', '>=', $lastCycleStartDate->toDateString())
                                                 ->sum('amount') * -1; // Sum and make positive

            // If payments made since the last cycle started are LESS than their fee,
            // then they haven't covered the upcoming cycle yet.
            if ($paymentsSinceLastCycle < $student->fees) {
                 $upcomingDuesCount++;
            }
        }
    }
    // --- End Upcoming Dues Calculation ---


    // 3. Fees Collected / Due (Overall)
    $totalCollected = Transaction::where('amount', '<', 0)->sum('amount') * -1;
    $totalDue = Transaction::sum('amount');

    // 4. Today's Attendance
    $totalPresentToday = Attendance::where('date', $today->toDateString())->where('status', 'present')->count();
    $totalAbsentToday = Attendance::where('date', $today->toDateString())->where('status', 'absent')->count();

    // 5. Monthly Growth Chart
    // ... (Chart logic remains the same) ...
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
        'upcomingDuesCount', // Corrected calculation
        'totalCollected',
        'totalDue',
        'totalPresentToday',
        'totalAbsentToday',
        'chartLabels',
        'chartData'
    ));
}
}