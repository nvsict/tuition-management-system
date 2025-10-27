@extends('layouts.app')

@section('title', 'Edit Attendance')
@section('header_title', 'Edit Attendance for ' . $selectedDateFormatted)

@section('content')
<!-- Success Message -->
@if (session('success'))
    <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow mb-4 transition-all duration-500" role="alert">
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Date and Batch Filter Form -->
<div class="bg-white rounded-xl shadow-md p-5 mb-6">
    <form method="GET" action="{{ route('attendance.edit') }}" class="flex flex-wrap items-end gap-4">
        {{-- Date Selector --}}
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Select Date:</label>
            <input type="date" name="date" id="date" value="{{ $selectedDate }}"
                   class="bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 cursor-pointer"
                   onchange="this.form.submit()"> {{-- Reload on date change --}}
        </div>

        {{-- Batch Selector --}}
        <div>
            <label for="batch_filter" class="block text-sm font-medium text-gray-700 mb-1">Select Batch:</label>
            <select name="batch_filter" id="batch_filter" onchange="this.form.submit()" {{-- Reload on batch change --}}
                class="bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 cursor-pointer">
                @forelse($batches as $batch)
                    <option value="{{ $batch->id }}" {{ $selectedBatchId == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                @empty
                    <option value="">No batches created yet</option>
                @endforelse
            </select>
        </div>
        {{-- Optional: Add a "Go" button if you prefer not to auto-reload --}}
    </form>
</div>

<!-- Attendance Form -->
<form action="{{ route('attendance.updatePast') }}" method="POST">
    @csrf
    {{-- Hidden fields to pass the selected date and batch back on save --}}
    <input type="hidden" name="date" value="{{ $selectedDate }}">
    <input type="hidden" name="batch_filter" value="{{ $selectedBatchId }}">

    <!-- Mark All Buttons -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <button type="button" id="markAllPresent" class="flex items-center gap-2 btn-start bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-300">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            Mark All Present
        </button>
        <button type="button" id="markAllAbsent" class="flex items-center gap-2 btn-stop bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-300">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            Mark All Absent
        </button>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr class="text-gray-600 uppercase text-sm font-semibold">
                        <th class="py-3 px-4 md:px-6 text-left">Roll No</th>
                        <th class="py-3 px-4 md:px-6 text-left">Name</th>
                        <th class="py-3 px-4 md:px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($attendanceList as $student)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-3 px-4 md:px-6">{{ $student['roll_no'] }}</td>
                            <td class="py-3 px-4 md:px-6 font-medium">{{ $student['name'] }}</td>
                            <td class="py-3 px-4 md:px-6 text-center">
                                {{-- Radio Button Pills --}}
                                <div class="inline-flex rounded-full shadow-sm overflow-hidden border border-gray-300">
                                    <input type="radio" id="present_{{ $student['id'] }}" name="attendance[{{ $student['id'] }}]" value="present" class="hidden peer/present" {{ $student['status'] == 'present' ? 'checked' : '' }} required>
                                    <label for="present_{{ $student['id'] }}" class="px-4 py-1 cursor-pointer peer-checked/present:bg-green-600 peer-checked/present:text-white hover:bg-green-100 transition-all text-gray-600">Present</label>

                                    <input type="radio" id="absent_{{ $student['id'] }}" name="attendance[{{ $student['id'] }}]" value="absent" class="hidden peer/absent" {{ $student['status'] == 'absent' ? 'checked' : '' }} required>
                                    <label for="absent_{{ $student['id'] }}" class="px-4 py-1 cursor-pointer peer-checked/absent:bg-red-600 peer-checked/absent:text-white hover:bg-red-100 transition-all text-gray-600 border-l border-gray-300">Absent</label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 px-4 text-center text-gray-500">No students found for this batch.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Save Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="flex items-center gap-2 btn-start bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300 text-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
            Save Changes for {{ Carbon\Carbon::parse($selectedDate)->format('M j') }}
        </button>
    </div>
</form>
@endsection

@push('scripts')
{{-- Include the same Mark All script as index.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const markAllPresentButton = document.getElementById('markAllPresent');
    const markAllAbsentButton = document.getElementById('markAllAbsent');

    // Select actual radio inputs by name pattern or value
    const presentRadios = document.querySelectorAll('input[type="radio"][value="present"]');
    const absentRadios = document.querySelectorAll('input[type="radio"][value="absent"]');

    if (markAllPresentButton) {
        markAllPresentButton.addEventListener('click', () => {
            presentRadios.forEach(r => r.checked = true);
        });
    }

    if (markAllAbsentButton) {
        markAllAbsentButton.addEventListener('click', () => {
            absentRadios.forEach(r => r.checked = true);
        });
    }

    // Auto-hide success message
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateY(-20px)';
        }, 3000);
        setTimeout(() => {
            successMessage.remove();
        }, 3500);
    }
});
</script>

@endpush