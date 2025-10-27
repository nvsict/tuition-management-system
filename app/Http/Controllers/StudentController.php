<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Batch;
use App\Models\Attendance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Needed for file operations
use Illuminate\Support\Str; // Needed for generating unique filenames

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query()->with('batch');
        if ($request->has('batch_filter') && $request->batch_filter != '') {
            $query->where('batch_id', $request->batch_filter);
        }
        $students = $query->orderBy('name')->paginate(15);
        $allBatches = Batch::orderBy('name')->get();
        return view('students.index', compact('students', 'allBatches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $student = new Student();
        $classes = range(setting('class_from', 6), setting('class_to', 12));
        $batches = Batch::orderBy('name')->get();
        return view('students.form', compact('student', 'classes', 'batches'));
    }

    /**
     * Store a newly created resource in storage.
     * Handles both file upload and base64 image data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class' => 'required|integer|min:'.setting('class_from', 6).'|max:'.setting('class_to', 12),
            'batch_id' => 'nullable|exists:batches,id',
            'phone' => 'required|string|numeric|digits:10',
            'fees' => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,gif|max:2048', // File upload validation
            'profile_picture_base64' => ['nullable', 'string', function ($attribute, $value, $fail) { // Base64 validation
                if ($value && !preg_match('/^data:image\/(png|jpeg|gif);base64,/', $value)) {
                    $fail('The captured photo is not a valid image format.');
                }
            }],
        ]);

        $validated['roll_no'] = 'CLS' . $validated['class'] . '-' . time();
        $validated['profile_picture_url'] = null; // Default to null

        // --- IMAGE HANDLING LOGIC ---
        $imagePath = null;
        if ($request->filled('profile_picture_base64')) {
            // Priority 1: Handle Base64 data (from camera)
            try {
                // Extract image data and type from base64 string
                list($type, $data) = explode(';', $request->profile_picture_base64);
                list(, $data)      = explode(',', $data);
                $imageData = base64_decode($data);
                
                // Determine file extension
                $extension = '';
                if (strpos($type, 'image/png') !== false) $extension = 'png';
                elseif (strpos($type, 'image/jpeg') !== false) $extension = 'jpg';
                elseif (strpos($type, 'image/gif') !== false) $extension = 'gif';
                else throw new \Exception('Invalid image type from base64'); // Fail validation if type unknown
                
                // Generate unique filename
                $filename = 'profile_pictures/student_' . Str::random(10) . '_' . time() . '.' . $extension;
                
                // Save the file to public storage
                Storage::disk('public')->put($filename, $imageData);
                $imagePath = $filename;

            } catch (\Exception $e) {
                // Log the error and potentially return with an error message
                 \Log::error("Base64 Image Save Error: " . $e->getMessage());
                 // Optionally add validation error back to the user
                 // return back()->withErrors(['profile_picture_base64' => 'Failed to save captured image. Please try again or upload a file.'])->withInput();
                 $imagePath = null; // Ensure path is null if saving failed
            }

        } elseif ($request->hasFile('profile_picture')) {
            // Priority 2: Handle File Upload
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $validated['profile_picture_url'] = $imagePath;
        // --- END IMAGE HANDLING ---


        Student::create($validated);
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    /**
     * Display the specified resource. (Redirects to profile)
     */
    public function show(Student $student)
    {
        return redirect()->route('students.profile', $student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = range(setting('class_from', 6), setting('class_to', 12));
        $batches = Batch::orderBy('name')->get();
        return view('students.form', compact('student', 'classes', 'batches'));
    }

    /**
     * Update the specified resource in storage.
     * Handles both file upload and base64 image data, deleting old file.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class' => 'required|integer|min:'.setting('class_from', 6).'|max:'.setting('class_to', 12),
            'batch_id' => 'nullable|exists:batches,id',
            'phone' => 'required|string|numeric|digits:10',
            'fees' => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'profile_picture_base64' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value && !preg_match('/^data:image\/(png|jpeg|gif);base64,/', $value)) {
                    $fail('The captured photo is not a valid image format.');
                }
            }],
        ]);

        // --- IMAGE HANDLING LOGIC ---
        $newImagePath = null;
        $deleteOldImage = false; // Flag to track if old image needs deletion

        if ($request->filled('profile_picture_base64')) {
            // Priority 1: Handle Base64 data
             try {
                list($type, $data) = explode(';', $request->profile_picture_base64);
                list(, $data)      = explode(',', $data);
                $imageData = base64_decode($data);
                $extension = '';
                if (strpos($type, 'image/png') !== false) $extension = 'png';
                elseif (strpos($type, 'image/jpeg') !== false) $extension = 'jpg';
                elseif (strpos($type, 'image/gif') !== false) $extension = 'gif';
                else throw new \Exception('Invalid image type from base64');
                
                $filename = 'profile_pictures/student_' . $student->id . '_' . time() . '.' . $extension; // Include student ID for clarity
                Storage::disk('public')->put($filename, $imageData);
                $newImagePath = $filename;
                $deleteOldImage = true; // Mark old image for deletion

            } catch (\Exception $e) {
                 \Log::error("Base64 Image Update Error (Student ID: {$student->id}): " . $e->getMessage());
                 $newImagePath = null; // Ensure path is null if failed
            }

        } elseif ($request->hasFile('profile_picture')) {
            // Priority 2: Handle File Upload
            $newImagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $deleteOldImage = true; // Mark old image for deletion
        }

        // Only update the path if a new image was successfully processed
        if ($newImagePath !== null) {
             // Delete the old picture *before* updating the record
             if ($deleteOldImage && $student->profile_picture_url) {
                 Storage::disk('public')->delete($student->profile_picture_url);
             }
             $validated['profile_picture_url'] = $newImagePath;
        }
        // If no new image, $validated['profile_picture_url'] remains unset, keeping the old URL.
        // --- END IMAGE HANDLING ---

        $student->update($validated);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * Also deletes the profile picture file.
     */
    public function destroy(Student $student)
    {
        try {
            // Delete profile picture if it exists
            if ($student->profile_picture_url) {
                Storage::disk('public')->delete($student->profile_picture_url);
            }

            $student->delete();
            return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            \Log::error("Error deleting student {$student->id}: " . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'Could not delete student. An unexpected error occurred.');
        }
    }

    /**
     * Display the profile page for a specific student.
     */
    public function profile(Student $student)
    {
        $student->load(['batch', 'transactions']);
        $attendanceRecords = Attendance::where('student_id', $student->id)->get();
        $totalPresent = $attendanceRecords->where('status', 'present')->count();
        $totalAbsent = $attendanceRecords->where('status', 'absent')->count();
        $totalDaysMarked = $totalPresent + $totalAbsent;
        $presentPercentage = ($totalDaysMarked > 0) ? round(($totalPresent / $totalDaysMarked) * 100, 1) : 0;
        
        $this->autoGenerateCharges($student);
        $balanceData = DB::table('transactions')->where('student_id', $student->id)->sum('amount');
        $currentBalance = $balanceData ?? 0;
        $student->transactions_sum_amount = $currentBalance;

        $attendanceSummary = (object)[
            'total_days' => $totalDaysMarked, 'total_present' => $totalPresent,
            'total_absent' => $totalAbsent, 'present_percentage' => $presentPercentage,
        ];
        $recentTransactions = $student->transactions()->orderBy('date', 'desc')->limit(10)->get();

        return view('students.profile', compact('student', 'attendanceSummary', 'currentBalance', 'recentTransactions'));
    }

    // =========================================================================
    //  PRIVATE HELPER FUNCTION (Copied from TransactionController)
    // =========================================================================
    private function autoGenerateCharges(Student $student)
     {
         $joinDate = $student->join_date;
         $monthlyFee = $student->fees;
         if (!$joinDate || $monthlyFee <= 0) return;
         $joinDate = Carbon::parse($joinDate);
         $lastCharge = Transaction::where('student_id', $student->id)->where('amount', '>', 0)->orderBy('date', 'desc')->first();
         $cursorDate = $lastCharge ? Carbon::parse($lastCharge->date) : $joinDate->copy();
         $billingDay = $joinDate->day;
         $nextBillingDate = $cursorDate->copy()->day($billingDay);
         if ($nextBillingDate->lte($cursorDate)) $nextBillingDate->addMonthNoOverflow();
         $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));
         while ($nextBillingDate->isPast() || $nextBillingDate->isToday()) {
             $periodStartDate = $nextBillingDate->copy()->subMonthNoOverflow();
             $periodStartDate->day(min($billingDay, $periodStartDate->daysInMonth));
             $periodEndDate = $nextBillingDate->copy()->subDay();
             $chargeExists = Transaction::where('student_id', $student->id)->where('amount', '>', 0)
                                     ->where('date', '>=', $periodStartDate->toDateString())
                                     ->where('date', '<', $nextBillingDate->toDateString())
                                     ->exists();
             if (!$chargeExists) {
                  Transaction::create([
                     'student_id' => $student->id,
                     'description' => 'Fee for ' . $periodStartDate->format('M d') . ' - ' . $periodEndDate->format('M d, Y'),
                     'amount' => $monthlyFee, 'date' => $periodStartDate->toDateString(),
                 ]);
             }
             $nextBillingDate->addMonthNoOverflow();
             $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));
         }
     }
}