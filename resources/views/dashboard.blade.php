@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
    {{-- Dashboard Card Section --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <style>
            .dashboard-card {
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                transition: transform 0.25s ease, box-shadow 0.25s ease;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .dashboard-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
            }

            .card-metric-value {
                font-size: 2.4rem;
                font-weight: 700;
            }
            .card-metric-label {
                color: #6b7280;
                font-size: 0.9rem;
                text-transform: uppercase;
                margin-bottom: 0.5rem;
            }

            .status-red-text { color: #ef4444; }
            .status-blue-text { color: #3b82f6; }
            .status-green-text { color: #10b981; }

            .card-red-border { border-left: 4px solid #ef4444; }
            .card-blue-border { border-left: 4px solid #3b82f6; }
            .card-green-border { border-left: 4px solid #10b981; }

            .attendance-value {
                font-size: 2.3rem;
                font-weight: 700;
            }
            .attendance-label {
                font-size: 1rem;
                font-weight: 500;
                color: #4b5563;
            }

            /* Chart + Attendance Layout */
            .widget-container {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            @media (min-width: 1024px) {
                .widget-container {
                    grid-template-columns: 1fr 2fr;
                    align-items: stretch;
                }
            }

            .chart-container {
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                padding: 1.5rem;
                position: relative;
                height: 350px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
        </style>

        <!-- Total Students -->
        <a href="{{ route('students.index') }}" class="block">
            <div class="dashboard-card p-6">
                <p class="card-metric-label">Total Students</p>
                <p class="card-metric-value text-gray-800">{{ $totalStudents }}</p>
                <span class="text-sm text-gray-500 mt-2">Currently enrolled</span>
            </div>
        </a>

        <!-- Students Overdue -->
        <a href="{{ route('fees.reminders') }}" class="block">
            <div class="dashboard-card p-6 card-red-border">
                <p class="card-metric-label status-red-text">Students Overdue</p>
                <p class="card-metric-value status-red-text">{{ $studentsOverdueCount }}</p>
                <span class="text-sm text-gray-500 mt-2">Action required</span>
            </div>
        </a>

        <!-- Fees Collected -->
        <a href="{{ route('fees.index') }}" class="block">
            <div class="dashboard-card p-6 card-green-border">
                <p class="card-metric-label status-green-text">Fees Collected</p>
                <p class="card-metric-value status-green-text">₹{{ number_format($totalCollected) }}</p>
                <span class="text-sm text-gray-500 mt-2">Today's collection</span>
            </div>
        </a>
    </section>

    {{-- Attendance + Chart Section --}}
    <section class="widget-container mb-10">
        <!-- Today's Attendance -->
        <a href="{{ route('attendance.index') }}" class="block">
            <div class="dashboard-card p-6 flex flex-col justify-between h-full">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Today's Attendance</h3>
                <div class="flex items-center justify-between mb-4">
                    <div class="text-center flex-1">
                        <p class="attendance-value text-green-600">{{ $totalPresentToday }}</p>
                        <p class="attendance-label">Present</p>
                    </div>
                    <div class="border-l h-16 mx-4"></div>
                    <div class="text-center flex-1">
                        <p class="attendance-value text-red-600">{{ $totalAbsentToday }}</p>
                        <p class="attendance-label">Absent</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500 text-center">Total enrolled: {{ $totalStudents }}</span>
            </div>
        </a>

        <!-- Monthly Student Growth Chart -->
        <div class="chart-container">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Monthly Student Growth</h2>
            <canvas id="growthChart"></canvas>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartEl = document.getElementById('growthChart');
        if (typeof Chart !== 'undefined' && chartEl) {
            const ctx = chartEl.getContext('2d');
            const chartLabels = @json($chartLabels);
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'New Students Joined',
                        data: chartData,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1f2937',
                            titleColor: '#fff',
                            bodyColor: '#d1d5db',
                            padding: 10,
                            cornerRadius: 6,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' },
                            ticks: { color: '#6b7280', stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280' }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
