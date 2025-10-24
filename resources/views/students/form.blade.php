@extends('layouts.app')

@section('title', $student->exists ? 'Edit Student' : 'Add Student')
@section('header_title', $student->exists ? 'Edit Student' : 'Add New Student')

@section('content')
    <div class="max-w-2xl mx-auto">
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
        
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" method="POST">
                @csrf
                @if($student->exists)
                    @method('PUT')
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <!-- BATCH DROPDOWN (NEW) -->
                    <div>
                        <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                        <select name="batch_id" id="batch_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select a Batch (Optional)</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ old('batch_id', $student->batch_id) == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CLASS DROPDOWN (Now more of a metadata field) -->
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class (e.g., 10, 11, 12)</label>
                        <select name="class" id="class" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}" {{ old('class', $student->class) == $class ? 'selected' : '' }}>
                                    Class {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile No (10 digits)</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $student->phone) }}" pattern="[0-9]{10}" title="Please enter a 10-digit mobile number" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div>
                        <label for="fees" class="block text-sm font-medium text-gray-700 mb-1">Fees per month (₹)</label>
                        <input type="number" name="fees" id="fees" value="{{ old('fees', $student->fees) }}" min="0" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
                        <input type="date" name="join_date" id="join_date" value="{{ old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d')) }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    @if($student->exists)
                        <div class="md:col-span-2">
                            <label for="roll_no" class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                            <input type="text" name="roll_no" id="roll_no" value="{{ old('roll_no', $student->roll_no) }}" class="block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100" readonly>
                            <p class="text-xs text-gray-500 mt-1">Roll No is auto-generated and cannot be changed.</p>
                        </div>
                    @endif
                </div>
                
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('students.index') }}" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        {{ $student->exists ? 'Update Student' : 'Save Student' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection