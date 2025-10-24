@extends('layouts.app')

@section('title', 'Batch Management')
@section('header_title', 'Batch Management')

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

    <div class="flex justify-end mb-4">
        <a href="{{ route('batches.create') }}" class="btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300 text-center">
            + Add New Batch
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Batch Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Time Slot</th>
                        <th class="py-3 px-4 md:px-6 text-center">Student Count</th>
                        <th class="py-3 px-4 md:px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($batches as $batch)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 md:px-6 text-left font-semibold">{{ $batch->name }}</td>
                            <td class="py-3 px-4 md:px-6 text-left">{{ $batch->time_slot ?? 'N/A' }}</td>
                            <td class="py-3 px-4 md:px-6 text-center">{{ $batch->students_count }}</td>
                            <td class="py-3 px-4 md:px-6 text-left flex gap-2">
                                <a href="{{ route('batches.edit', $batch) }}" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                <form action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 px-4 text-center text-gray-500">No batches found. Add one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection