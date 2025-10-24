<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Batch; // This should already be here
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query()->with('batch'); // Eager load batch info
        
        // UPDATED: Filter by Batch instead of Class
        if ($request->has('batch_filter') && $request->batch_filter != '') {
            $query->where('batch_id', $request->batch_filter);
        }
        
        $students = $query->orderBy('name')->paginate(15);
        
        // UPDATED: Get all batches for the filter dropdown
        $allBatches = Batch::orderBy('name')->get(); 
        
        // Pass the new $allBatches variable to the view
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
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class' => 'required|integer|min:'.setting('class_from', 6).'|max:'.setting('class_to', 12),
            'batch_id' => 'nullable|exists:batches,id',
            'phone' => 'required|string|numeric|digits:10|unique:students',
            'fees' => 'required|numeric|min:0',
            'join_date' => 'required|date',
        ]);
        
        $validated['roll_no'] = 'CLS' . $validated['class'] . '-' . time();
        
        Student::create($validated);
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return redirect()->route('students.edit', $student);
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
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class' => 'required|integer|min:'.setting('class_from', 6).'|max:'.setting('class_to', 12),
            'batch_id' => 'nullable|exists:batches,id',
            'phone' => 'required|string|numeric|digits:10|unique:students,phone,' . $student->id,
            'fees' => 'required|numeric|min:0',
            'join_date' => 'required|date',
        ]);
        
        $student->update($validated);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('students.index')->with('error', 'Could not delete student. They may have related records.');
        }
    }
}