<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Batch; // <-- ADD THIS
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Show the attendance marking page.
     */
    public function index(Request $request)
    {
        // CHANGED: Get all batches, not classes
        $batches = Batch::orderBy('name')->get();
        
        // Default to the first batch in the list, or null if no batches exist
        $defaultBatchId = $batches->first()->id ?? null;
        
        // Get the selected batch ID from the request, or use the default
        $selectedBatchId = $request->input('batch_filter', $defaultBatchId);
        $today = Carbon::today()->toDateString();

        // Get all students for the selected BATCH
        $students = Student::where('batch_id', $selectedBatchId)->orderBy('name')->get();

        // Get existing attendance records for these students FOR TODAY
        $existingAttendance = Attendance::where('date', $today)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Combine student info with their attendance status
        $attendanceList = $students->map(function ($student) use ($existingAttendance) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'roll_no' => $student->roll_no,
                'status' => $existingAttendance[$student->id]->status ?? null,
            ];
        });

        return view('attendance.index', [
            'attendanceList' => $attendanceList,
            'batches' => $batches, // <-- CHANGED
            'selectedBatchId' => $selectedBatchId, // <-- CHANGED
            'today' => Carbon::today()->format('D, M j, Y'),
        ]);
    }

    /**
     * Store the attendance records for the day.
     */
    public function store(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent',
            'date' => 'required|date',
        ]);

        $today = Carbon::parse($request->date)->toDateString();

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $today,
                ],
                [
                    'status' => $status
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance saved successfully!');
    }

    /**
     * Show the attendance report page.
     */
    public function report(Request $request)
    {
        // CHANGED: Get batches and students
        $allBatches = Batch::orderBy('name')->get();
        $allStudents = Student::orderBy('name')->get();

        // Get filter values from the request, providing defaults
        $selectedBatch = $request->input('batch_filter'); // <-- CHANGED
        $selectedStudent = $request->input('student_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Start building the query
        $query = Student::query();

        // Apply filters if they are provided
        if ($selectedBatch) { // <-- CHANGED
            $query->where('batch_id', $selectedBatch);
        }
        if ($selectedStudent) {
            $query->where('id', $selectedStudent);
        }

        // Eager load relationships
        $studentsForReport = $query->with([
            'attendance' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }, 
            'batch' // <-- ADD THIS
        ])->get();

        // Calculate stats for each student
        $reportData = $studentsForReport->map(function ($student) {
            $totalPresent = $student->attendance->where('status', 'present')->count();
            $totalAbsent = $student->attendance->where('status', 'absent')->count();
            $totalDays = $totalPresent + $totalAbsent;
            $presentPercentage = ($totalDays > 0) ? ($totalPresent / $totalDays) * 100 : 0;

            return (object)[
                'name' => $student->name,
                'class' => $student->class,
                'roll_no' => $student->roll_no,
                'batch_name' => $student->batch->name ?? 'N/A', // <-- ADD THIS
                'total_days' => $totalDays,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'present_percentage' => round($presentPercentage, 1),
            ];
        });

        return view('attendance.report', [
            'reportData' => $reportData,
            'allBatches' => $allBatches, // <-- CHANGED
            'allStudents' => $allStudents,
            'filters' => [
                'selectedBatch' => $selectedBatch, // <-- CHANGED
                'selectedStudent' => $selectedStudent,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]
        ]);
    }

    /**
     * Export the filtered attendance report to a CSV file.
     */
    public function exportCsv(Request $request)
    {
        // --- 1. Get the exact same data as the report() method ---
        $selectedBatch = $request->input('batch_filter'); // <-- CHANGED
        $selectedStudent = $request->input('student_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = Student::query();

        if ($selectedBatch) { // <-- CHANGED
            $query->where('batch_id', $selectedBatch);
        }
        if ($selectedStudent) {
            $query->where('id', $selectedStudent);
        }

        $studentsForReport = $query->with([
            'attendance' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            },
            'batch' // <-- ADD THIS
        ])->get();

        $reportData = $studentsForReport->map(function ($student) {
            $totalPresent = $student->attendance->where('status', 'present')->count();
            $totalAbsent = $student->attendance->where('status', 'absent')->count();
            $totalDays = $totalPresent + $totalAbsent;
            $presentPercentage = ($totalDays > 0) ? ($totalPresent / $totalDays) * 100 : 0;

            return (object)[
                'name' => $student->name,
                'class' => $student->class,
                'roll_no' => $student->roll_no,
                'batch_name' => $student->batch->name ?? 'N/A', // <-- ADD THIS
                'total_days' => $totalDays,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'present_percentage' => round($presentPercentage, 1),
            ];
        });

        // --- 2. Create and stream the CSV file ---
        $fileName = "attendance-report-" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // Add Header Row
            fputcsv($file, [
                'Student Name', 
                'Batch Name', // <-- ADDED
                'Class', 
                'Roll No', 
                'Total Days (in range)', 
                'Present', 
                'Absent', 
                'Present %'
            ]);

            // Add Data Rows
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row->name,
                    $row->batch_name, // <-- ADDED
                    $row->class,
                    $row->roll_no,
                    $row->total_days,
                    $row->total_present,
                    $row->total_absent,
                    $row->present_percentage . '%'
                ]);
            }
            fclose($file);
        };

        // Return the file as a download
        return response()->stream($callback, 200, $headers);
    }
}