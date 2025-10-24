@extends('layouts.app')

@section('title', 'Add Fee Payment')
@section('header_title', 'Add Fee Payment')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <ul class="list-disc ml-6 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- The Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="{{ route('fees.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Student Dropdown -->
                    <div class="md:col-span-2">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                        <select name="student_id" id="student_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select a student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} (Class {{ $student->class }}) - Fee: ₹{{ $student->fees }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount Paid -->
                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₹)</label>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" min="0" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                    <!-- Month -->
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Payment For Month</label>
                        <select name="month" id="month" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            @foreach($months as $month)
                                <option value="{{ $month }}" {{ old('month', $currentMonth) == $month ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Payment For Year</label>
                        <input type="number" name="year" id="year" value="{{ old('year', $currentYear) }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('fees.index') }}" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
