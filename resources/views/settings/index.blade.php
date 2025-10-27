@extends('layouts.app')

@section('title', 'Settings')
@section('header_title', 'Application Settings')

@section('content')
    <div class="max-w-xl mx-auto px-4 py-8">

        <!-- Success Message -->
        @if (session('success'))
            <div class="flex items-center p-3 mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg shadow-sm animate-fade-in" role="alert">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-medium">Settings saved successfully!</p>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-sm animate-fade-in" role="alert">
                <p class="font-medium mb-1">Please correct the following errors:</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Container -->
        <div class="bg-white rounded-lg shadow-xl border border-gray-100 divide-y divide-gray-200">
            <!-- Header -->
            <div class="px-6 py-4">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </h1>
                <p class="mt-1 text-sm text-gray-500">Adjust your application's core configuration.</p>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" x-data="{ isSaving: false }" @submit="isSaving = true">
                @csrf

                <!-- Section: Institute Details -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Institute</h2>
                    <div class="space-y-5">
                        {{-- Institute Name --}}
                        <div>
                            <label for="institute_name" class="block text-sm font-medium text-gray-700">Institute Name</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" name="institute_name" id="institute_name"
                                       value="{{ old('institute_name', setting('institute_name', 'My Tuition Center')) }}"
                                       class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md text-base focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="e.g., Bright Minds Academy">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Name shown in headers and reports.</p>
                        </div>

                        {{-- Institute Logo URL --}}
                        <div>
                            <label for="institute_logo_url" class="block text-sm font-medium text-gray-700">Institute Logo URL</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="url" name="institute_logo_url" id="institute_logo_url"
                                       value="{{ old('institute_logo_url', setting('institute_logo_url')) }}"
                                       placeholder="https://example.com/logo.png"
                                       class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md text-base focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L15 15m-2-6l2 2m0 0l2.586-2.586a2 2 0 012.828 0M12 15h.01M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Direct link to a square or circular logo image.</p>
                            @if (setting('institute_logo_url'))
                                <div class="mt-2 flex items-center">
                                    <span class="text-xs text-gray-600 mr-2">Current:</span>
                                    <img src="{{ setting('institute_logo_url') }}" alt="Current Logo" class="h-8 w-8 object-contain rounded-full border border-gray-200 p-0.5 bg-white">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section: Academic Settings -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Academic & Billing</h2>

                    <div class="p-3 mb-5 bg-yellow-50 border border-yellow-200 rounded-md text-yellow-800 text-sm">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="font-medium">Important:</span>
                        </div>
                        <p class="mt-1 ml-7 text-xs">Class range affects student and attendance options globally.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        {{-- Current Session Year --}}
                        <div>
                            <label for="session_year" class="block text-sm font-medium text-gray-700">Session Year</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" name="session_year" id="session_year"
                                       value="{{ old('session_year', setting('session_year', date('Y') . '-' . (date('y') + 1))) }}"
                                       placeholder="2025-2026"
                                       class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md text-base focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Class (From) --}}
                        <div>
                            <label for="class_from" class="block text-sm font-medium text-gray-700">Class From</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="class_from" id="class_from"
                                       value="{{ old('class_from', setting('class_from', 6)) }}"
                                       class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md text-base focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       min="1" max="12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4h-3m3 4h3m-6 0a2 2 0 110 4m0-4H7m0 0H4m7 6v-3m0 3v3m0-3h3m0 0h-3m3 0a2 2 0 110 4m0-4H7m0 0H4"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Class (To) --}}
                        <div>
                            <label for="class_to" class="block text-sm font-medium text-gray-700">Class To</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="class_to" id="class_to"
                                       value="{{ old('class_to', setting('class_to', 12)) }}"
                                       class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md text-base focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       min="1" max="12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4h-3m3 4h3m-6 0a2 2 0 110 4m0-4H7m0 0H4m7 6v-3m0 3v3m0-3h3m0 0h-3m3 0a2 2 0 110 4m0-4H7m0 0H4"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions & Progress -->
                <div class="p-6 bg-gray-50 flex justify-end items-center">
                    {{-- Progress Spinner --}}
                    <div x-show="isSaving" x-cloak class="flex items-center text-gray-600 mr-4">
                        <svg class="animate-spin h-5 w-5 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </div>

                    {{-- Save Button --}}
                    <button type="submit"
                            :disabled="isSaving"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                            :class="{'opacity-50 cursor-not-allowed': isSaving}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7m-2 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m5 0h.01M12 21a3 3 0 003-3H9a3 3 0 003 3z"></path></svg>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
{{-- No Font Awesome CDN needed for this minimal design as we are using heroicons built into Tailwind CSS --}}
@endpush