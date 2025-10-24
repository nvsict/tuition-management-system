@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Students Card -->
        <a href="{{ route('students.index') }}" class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                <h2 class="text-sm font-medium text-gray-500 uppercase">Total Students</h2>
                <p class="text-4xl font-bold text-gray-900">{{ $totalStudents }}</p>
            </div>
        </a>

        <!-- Students Overdue Card (NEW) -->
        <a href="{{ route('fees.reminders') }}" class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-red-100 border border-red-300 p-6 rounded-lg shadow-lg h-full">
                <h2 class="text-sm font-medium text-red-700 uppercase">Students Overdue</h2>
                <p class="text-4xl font-bold text-red-700">{{ $studentsOverdueCount }}</p>
            </div>
        </a>
        <!-- Upcoming Dues Card (NEW) -->
        <a href="{{ route('fees.index') }}" {{-- Link to main ledger for now --}}
           class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-blue-100 border border-blue-300 p-6 rounded-lg shadow-lg h-full">
                <h2 class="text-sm font-medium text-blue-700 uppercase">Dues This Week</h2>
                <p class="text-4xl font-bold text-blue-700">{{ $upcomingDuesCount }}</p>
            </div>
        </a>
        <!-- Fees Collected Card -->
        <a href="{{ route('fees.index') }}" class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                 <h2 class="text-sm font-medium text-gray-500 uppercase">Fees Collected</h2>
                <p class="text-4xl font-bold text-green-600">₹{{ number_format($totalCollected) }}</p>
           </div>
        </a>

        <!-- Fees Pending Card -->
        {{-- You might want to remove this one now that you have "Students Overdue" --}}
        {{-- <a href="{{ route('fees.index') }}" class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                <h2 class="text-sm font-medium text-gray-500 uppercase">Total Fees Due</h2>
                <p class="text-4xl font-bold text-red-600">₹{{ number_format($totalDue) }}</p>
            </div>
        </a> --}}

        <!-- Today's Attendance Card -->
        <a href="{{ route('attendance.index') }}" class="block transform hover:-translate-y-1 transition-transform duration-300">
            <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                <h2 class="text-sm font-medium text-gray-500 uppercase">Today's Attendance</h2>
                <div class="flex items-baseline space-x-4">
                    <p class="text-4xl font-bold text-gray-900">{{ $totalPresentToday }}</p>
                    <span class="text-xl text-gray-500">Present</span>
                    <p class="text-2xl font-bold text-gray-500">{{ $totalAbsentToday }}</p>
                    <span class="text-md text-gray-500">Absent</span>
                </div>
            </div>
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Monthly Student Growth</h2>
        <canvas id="growthChart" height="100"></canvas>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        if (typeof Chart !== 'undefined' && document.getElementById('growthChart')) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const chartLabels = @json($chartLabels);
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'New Students Joined',
                        data: chartData,
                        backgroundColor: 'rgba(39, 141, 39, 0.2)',
                        borderColor: '#278d27',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    },
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush