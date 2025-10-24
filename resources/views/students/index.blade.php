@extends('layouts.app')

@section('title', 'Student Management')
@section('header_title', 'Student Management')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <a href="{{ route('students.create') }}" class="w-full md:w-auto btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300 text-center">
            + Add New Student
        </a>
        
        <!-- UPDATED: Filter Form now uses Batches -->
        <form method="GET" action="{{ route('students.index') }}" class="w-full md:w-auto flex items-center gap-2">
            <select name="batch_filter" class="block w-full bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">All Batches</option>
                @foreach($allBatches as $batch)
                    <option value="{{ $batch->id }}" {{ request('batch_filter') == $batch->id ? 'selected' : '' }}>
                        {{ $batch->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-gray-800 transition duration-300">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th> <!-- ADDED -->
                        <th class="py-3 px-4 md:px-6 text-left">Class</th>
                        <th class="py-3 px-4 md:px-6 text-left">Phone</th>
                        <th class="py-3 px-4 md:px-6 text-left">Fees</th>
                        <th class="py-3 px-4 md:px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($students as $student)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer" 
                            data-href="{{ route('students.edit', $student) }}">
                            
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap">{{ $student->name }}</td>
                            <td class="py-3 px-4 md:px-6 text-left">{{ $student->batch->name ?? 'N/A' }}</td> <!-- ADDED -->
                            <td class="py-3 px-4 md:px-6 text-left">{{ $student->class }}</td>
                            <td class="py-3 px-4 md:px-6 text-left">{{ $student->phone }}</td>
                            <td class="py-3 px-4 md:px-6 text-left">₹{{ number_format($student->fees) }}</td>
                            
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap">
                                <div class="flex gap-3 items-center">
                                    <a href="{{ route('fees.index', ['student_id' => $student->id]) }}"
   onclick="event.stopPropagation()"
   class="btn-start text-white text-xs font-bold py-1 px-3 rounded-full shadow hover:bg-green-700 transition duration-300">
    Pay Fee
</a>
                                    <a href="{{ route('students.edit', $student) }}" 
                                       onclick="event.stopPropagation()"
                                       class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this student?');"
                                          onclick="event.stopPropagation()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <!-- UPDATED: Colspan is now 7 -->
                            <td colspan="7" class="py-6 px-4 text-center text-gray-500">No students found. Add one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-white">
            {{ $students->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('tr[data-href]');
        
        rows.forEach(row => {
            row.addEventListener('click', () => {
                window.location.href = row.dataset.href;
            });
        });
    });
</script>
@endpush