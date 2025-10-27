@extends('layouts.app')

@section('title', 'Attendance Report')
@section('header_title', 'Attendance Report')

@section('content')

    <div x-data="{ reportUrl: '{{ route('attendance.report') }}' }">
        
        <!-- Filter Card (IMPROVED STYLING, STICKY) -->
        <div class="bg-white rounded-xl shadow-xl p-6 mb-6 sticky top-0 z-10 transition-all duration-300 transform hover:shadow-2xl">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Filter & Search</h2>
            
            <form id="filterForm" method="GET" action="{{ route('attendance.report') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                
                <!-- Batch Filter -->
                <div>
                    <label for="batch_filter" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <select name="batch_filter" id="batch_filter" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-150">
                        <option value="">All Batches</option>
                        @foreach($allBatches as $batch)
                            <option value="{{ $batch->id }}" {{ $filters['selectedBatch'] == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Student Search Input (NEW LIVE SEARCH) -->
                <div class="lg:col-span-1">
                    <label for="student_search" class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
                    <input type="text" id="student_search" placeholder="Type name..." class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-150">
                </div>
                
                <!-- Student Filter (Hidden, managed by search input for backend filtering) -->
                <input type="hidden" name="student_id" id="student_id_hidden" value="{{ $filters['selectedStudent'] }}">
                
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $filters['startDate'] }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-150">
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $filters['endDate'] }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-150">
                </div>

                <!-- Buttons -->
                <div class="lg:col-span-1 flex gap-3 mt-2">
                    <!-- Generate Button -->
                    <button type="submit" class="btn-start flex items-center justify-center text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-150 w-full">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Report
                    </button>
                    <!-- Clear Filters Button with Tooltip -->
                    <a href="{{ route('attendance.report') }}" 
                       x-tooltip.placement.bottom="'Clear all filters'"
                       class="btn-stop flex items-center justify-center text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-orange-700 transition duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden relative">
            <div id="table-container" class="overflow-x-auto h-auto max-h-[70vh]">
                <table id="reportTable" class="min-w-full leading-normal">
                    <thead class="sticky top-0 bg-gray-100 z-10 shadow-md">
                        <tr class="text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 md:px-6 text-left">Student Name</th>
                            <th class="py-3 px-4 md:px-6 text-left">Batch</th>
                            <th class="py-3 px-4 md:px-6 text-left">Class</th>
                            <th class="py-3 px-4 md:px-6 text-center">Total Days</th>
                            <th class="py-3 px-4 md:px-6 text-center">Present</th>
                            <th class="py-3 px-4 md:px-6 text-center">Absent</th>
                            <th class="py-3 px-4 md:px-6 text-center">Present %</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse ($reportData as $data)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-100 student-row" 
                                data-name="{{ strtolower($data->name) }}"
                                x-data="{ tooltip: '{{ $data->present_percentage }}% Present' }">
                                
                                <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap font-semibold">
                                    {{ $data->name }}
                                </td>
                                <td class="py-3 px-4 md:px-6 text-left">{{ $data->batch_name }}</td>
                                <td class="py-3 px-4 md:px-6 text-left">{{ $data->class }}</td>
                                <td class="py-3 px-4 md:px-6 text-center font-bold">{{ $data->total_days }}</td>
                                <td class="py-3 px-4 md:px-6 text-center text-green-700">{{ $data->total_present }}</td>
                                <td class="py-3 px-4 md:px-6 text-center text-red-700">{{ $data->total_absent }}</td>

                                @php
                                    $percent_class = 'text-gray-500';
                                    $tooltip_text = "Data too sparse";
                                    if ($data->total_days > 0) {
                                        if ($data->present_percentage >= 90) { 
                                            $percent_class = 'text-green-700';
                                            $tooltip_text = "Excellent Attendance!";
                                        } elseif ($data->present_percentage >= 70) { 
                                            $percent_class = 'text-orange-600'; 
                                            $tooltip_text = "Average Attendance. Needs attention.";
                                        } else { 
                                            $percent_class = 'text-red-700';
                                            $tooltip_text = "Critical Attendance. Serious risk.";
                                        }
                                    }
                                @endphp
                                
                                <td class="py-3 px-4 md:px-6 text-center font-bold text-lg {{ $percent_class }}"
                                    x-tooltip.placement.left="{{ $tooltip_text }}">
                                    {{ $data->present_percentage }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 px-4 text-center text-gray-500">
                                    <div class="text-2xl">🔍</div>
                                    <p class="mt-2 font-semibold">No attendance data found for the selected filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Export Success Feedback (NEW) -->
            <div id="export-message" class="hidden absolute bottom-0 right-0 m-4 p-3 bg-blue-600 text-white rounded-lg shadow-xl transition duration-300">
                <p>Download started! Check your downloads folder.</p>
            </div>
            
            <!-- Export to CSV Button is now an independent element for JavaScript control -->
            <div class="p-4 bg-gray-50 flex justify-end">
                <a href="{{ route('attendance.export', request()->query()) }}" id="exportButton"
                   class="bg-blue-600 flex items-center text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export to CSV
                </a>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentSearch = document.getElementById('student_search');
        const reportTable = document.getElementById('reportTable');
        const rows = document.querySelectorAll('.student-row');
        const exportButton = document.getElementById('exportButton');
        const exportMessage = document.getElementById('export-message');

        // --- 1. Live Search (Client-Side Filtering) ---
        if (studentSearch) {
            studentSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                rows.forEach(row => {
                    const studentName = row.dataset.name;
                    if (studentName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // --- 2. Export Button Feedback ---
        if (exportButton) {
            exportButton.addEventListener('click', function(e) {
                // Display feedback message
                exportMessage.classList.remove('hidden');
                exportMessage.classList.add('opacity-100');
                
                // Hide the message after a few seconds
                setTimeout(() => {
                    exportMessage.classList.remove('opacity-100');
                    exportMessage.classList.add('hidden');
                }, 4000);
            });
        }
        
        // --- 3. Sticky Header UX ---
        // This makes the filter form sticky at the top of the viewport
        const filterCard = document.querySelector('.sticky.top-0');
        if (filterCard) {
            // Apply a subtle change when the table is scrolled to confirm stickiness
            const tableContainer = document.getElementById('table-container');
            if (tableContainer) {
                tableContainer.addEventListener('scroll', function() {
                    if (this.scrollTop > 0) {
                        filterCard.classList.add('border-b', 'border-gray-300');
                    } else {
                        filterCard.classList.remove('border-b', 'border-gray-300');
                    }
                });
            }
        }
        
        // --- 4. Tooltips (requires Alpine.js for x-tooltip) ---
        // Alpine.js is included in the main layout, so the x-tooltip directives
        // on the Present % cells should work automatically.
    });
</script>
@endpush