<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::withCount('students')->get();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $batch = new Batch();
        return view('batches.form', compact('batch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'time_slot' => 'nullable|string|max:255',
        ]);

        Batch::create($validated);
        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        return view('batches.form', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'time_slot' => 'nullable|string|max:255',
        ]);

        $batch->update($validated);
        return redirect()->route('batches.index')->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        // You might want to check if a batch has students before deleting
        if ($batch->students()->count() > 0) {
            return redirect()->route('batches.index')->with('error', 'Cannot delete batch. Please re-assign students first.');
        }

        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
    }
}