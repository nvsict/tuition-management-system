<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tuition Manager')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Inter font for modern typography --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Chart.js for graphs --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine.js for dynamic UI components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome (Needed for Validation Icons) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Base styles */
        body {
            font-family: 'Inter', sans-serif; /* Apply Inter font globally */
            background-color: #f8faff; /* Lighter background from new design */
        }

        /* Custom styles for the new design */
        .sidebar-bg {
            background-color: #2c3e50; /* Darker, modern sidebar color */
        }
        /* Base color and hover state */
        .sidebar-item {
            color: #b0c4de;
            transition: background-color 0.2s, color 0.2s, transform 0.1s;
        }
        /* Active state */
        .sidebar-item.active {
            background-color: #3f5872; /* Active background */
            color: #ffffff; /* Active text color */
            font-weight: 600;
        }
        /* General hover state for all items */
        .sidebar-item:hover:not(.active) {
            background-color: #34495e;
            color: #ffffff;
            transform: scale(1.01);
        }

        .sidebar-logo-text {
            color: #ffffff;
        }
        .sidebar-logo-highlight {
            color: #60a5fa; /* Tailwind blue-400 for highlight */
        }
        .btn-start { background-color: #10b981; } /* Tailwind green-500 */
        .btn-stop { background-color: #f59e0b; } /* Tailwind amber-500 */

        /* Specific styles for dropdowns within the sidebar to match new design */
        .sidebar-dropdown-item {
            color: #b0c4de;
            transition: background-color 0.2s, color 0.2s;
        }
        .sidebar-dropdown-item.active {
            background-color: #3f5872;
            color: #ffffff;
            font-weight: 500;
        }
        .sidebar-dropdown-item:hover:not(.active) {
            background-color: #3f5872;
            color: #ffffff;
        }

        .sidebar-logo-border {
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen bg-gray-200">
        <aside class="w-64 sidebar-bg text-white flex-shrink-0">
            <div class="p-6 text-center border-b sidebar-logo-border">
                
                {{-- DYNAMIC LOGO PREVIEW --}}
                @if(setting('institute_logo_url'))
                    <img src="{{ setting('institute_logo_url') }}" alt="Logo" class="w-20 h-20 rounded-full mx-auto mb-3 object-cover shadow-lg">
                @endif
                
                <h1 class="text-2xl font-bold sidebar-logo-text tracking-tight">
    @php
        // Get the institute name from settings or use the default
        $full_name = setting('institute_name', 'Tuition Manager');
        // Split the name in half for the branding effect (e.g., "Tuition" and "Manager")
        $words = explode(' ', $full_name, 2);
        $firstWord = $words[0] ?? 'Edu';
        $secondPart = $words[1] ?? 'Manager';
    @endphp
    <span class="sidebar-logo-highlight">{{ $firstWord }}</span>{{ $secondPart }}
</h1>
            </div>

            <nav class="mt-4">
                <ul class="space-y-2 px-4">
                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg
                                   {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Students --}}
                    <li>
                        <a href="{{ route('students.index') }}"
                           class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg
                                   {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.995 5.995 0 0012 12a5.995 5.995 0 00-3-5.197M15 21a9 9 0 00-9-9"></path></svg>
                            <span>Students</span>
                        </a>
                    </li>
                    
                    {{-- Batches --}}
                    <li>
                        <a href="{{ route('batches.index') }}"
                           class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg
                                   {{ request()->routeIs('batches.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-2.356M17 20H7m10 0v-2c0-1.657-1.343-3-3-3s-3 1.343-3 3v2m6 0H7m10 0v-2a3 3 0 00-5.356-2.356M12 11a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0zM6 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Batches</span>
                        </a>
                    </li>
                    
                    {{-- Attendance Dropdown --}}
                    <li x-data="{ open: {{ request()->routeIs('attendance.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-4 py-2 text-left rounded-lg {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <span class="flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Attendance</span>
                            </span>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="mt-2 space-y-2 pl-10">
                            <a href="{{ route('attendance.index') }}" class="sidebar-dropdown-item block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                                Mark Attendance
                            </a>
                            <a href="{{ route('attendance.report') }}" class="sidebar-dropdown-item block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                                View Report
                            </a>
                            {{-- NEW LINK --}}
                        <a href="{{ route('attendance.edit') }}" class="sidebar-dropdown-item block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('attendance.edit') ? 'active' : '' }}">
                            Edit Past Attendance
                        </a>
                        </div>
                    </li>

                    {{-- Fees Dropdown --}}
                    <li x-data="{ open: {{ request()->routeIs('fees.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="sidebar-item flex items-center justify-between w-full px-4 py-2 text-left rounded-lg {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                            <span class="flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Fees</span>
                            </span>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition class="mt-2 space-y-2 pl-10">
                            <a href="{{ route('fees.index') }}" class="sidebar-dropdown-item block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('fees.index') ? 'active' : '' }}">
                                Fee Ledger
                            </a>
                            <a href="{{ route('fees.reminders') }}" class="sidebar-dropdown-item block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('fees.reminders') ? 'active' : '' }}">
                                Reminder List
                            </a>
                        </div>
                    </li>

                    {{-- Settings --}}
                    <li>
                        <a href="{{ route('settings.index') }}"
                           class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg
                                   {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.573-1.066z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        @yield('header_title', 'Overview')
                    </h2>
                    {{-- User profile/logout section --}}
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-4 font-medium">Mohit Tarkar</span>
                        <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white font-semibold shadow-inner">MT</div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>