<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tuition Manager')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Your custom colors from the blueprint */
        .btn-start { background-color: #278d27; }
        .btn-stop { background-color: #f97d09; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen bg-gray-200">
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-6 text-center border-b border-gray-700">
                @if(setting('institute_logo_url'))
                    <img src="{{ setting('institute_logo_url') }}" alt="Logo" class="w-20 h-20 rounded-full mx-auto mb-3">
                @endif
                <h1 class="text-2xl font-bold text-white tracking-tight">
                    {{ setting('institute_name', 'Tuition Mgr') }}
                </h1>
            </div>
            
            <nav class="mt-4">
                <ul class="space-y-2 px-4">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition duration-200
                                  {{ request()->routeIs('dashboard') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('students.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition duration-200
                                  {{ request()->routeIs('students.*') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.995 5.995 0 0012 12a5.995 5.995 0 00-3-5.197M15 21a9 9 0 00-9-9"></path></svg>
                            <span>Students</span>
                        </a>
                    </li>
                    <!-- NEW BATCHES LINK -->
                    <li>
                        <a href="{{ route('batches.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition duration-200
                                  {{ request()->routeIs('batches.*') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-2.356M17 20H7m10 0v-2c0-1.657-1.343-3-3-3s-3 1.343-3 3v2m6 0H7m10 0v-2a3 3 0 00-5.356-2.356M12 11a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0zM6 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Batches</span>
                        </a>
                    </li>
                    <li x-data="{ open: {{ request()->routeIs('attendance.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-left rounded-lg transition duration-200 {{ request()->routeIs('attendance.*') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                            <span class="flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Attendance</span>
                            </span>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="mt-2 space-y-2 pl-10">
                            <a href="{{ route('attendance.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('attendance.index') ? 'text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                Mark Attendance
                            </a>
                            <a href="{{ route('attendance.report') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('attendance.report') ? 'text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                View Report
                            </a>
                        </div>
                    </li>

                    <!-- Fees Dropdown -->
                <li x-data="{ open: {{ request()->routeIs('fees.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-left rounded-lg transition duration-200 {{ request()->routeIs('fees.*') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
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
                        <a href="{{ route('fees.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('fees.index') ? 'text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                            Fee Ledger
                        </a>
                        <a href="{{ route('fees.reminders') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('fees.reminders') ? 'text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                            Reminder List
                        </a>
                    </div>
                </li>
                    
                    <li>
                        <a href="{{ route('settings.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition duration-200
                                  {{ request()->routeIs('settings.index') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.573-1.066z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-md p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-semibold text-gray-700">
                        @yield('header_title', 'Overview')
                    </h2>
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