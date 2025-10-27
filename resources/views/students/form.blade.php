@extends('layouts.app')

@section('title', $student->exists ? 'Edit Student' : 'Add Student')
@section('header_title', $student->exists ? 'Edit Student' : 'Add New Student')

@section('content')

<style>
    /* Adjust validation icon alignment */
    .relative > span.text-lg {
        top: 67% !important;
        transform: translateY(-50%) !important;
        right: 0.75rem; /* equivalent to right-3 */
    }

    /* Ensure icon doesn’t overlap text when user types */
    .relative input,
    .relative select {
        padding-right: 2.25rem !important; /* leaves space for icon */
    }

    /* Optional: smooth transition for color change */
    .relative > span.text-lg i {
        transition: color 0.2s ease;
    }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-8 shadow-sm animate-fade-in" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <strong class="font-semibold text-lg">Whoops! There were some problems:</strong>
            </div>
            <ul class="list-disc ml-8 mt-3 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
        {{-- Form Header --}}
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-extrabold text-gray-900">{{ $student->exists ? 'Edit Student Details' : 'Add New Student' }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $student->exists ? 'Update the student information below.' : 'Fill in the details to add a new student.' }}
            </p>
        </div>

        <form action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" method="POST" enctype="multipart/form-data" id="studentForm" class="p-6 md:p-8" x-data="studentForm()">
            @csrf
            @if($student->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                
                {{-- Profile Picture Section --}}
                <div class="md:col-span-2 p-4 bg-gray-50 rounded-xl transition">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>

                    <div class="flex flex-col md:flex-row items-center gap-6">
                        {{-- Live Capture Section --}}
                        <div class="flex flex-col items-center relative">
                            {{-- Placeholder Circle --}}
                            <div id="captureContainer"
                                 class="w-32 h-32 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-white shadow-sm">
                                <video id="camera" autoplay playsinline class="hidden w-full h-full object-cover"></video>
                                <canvas id="snapshot" class="hidden w-full h-full object-cover"></canvas>
                                <span id="placeholderText" class="text-xs text-gray-400 text-center">Live Preview</span>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex gap-2 mt-3">
                                <button type="button" id="startCamera"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Start Camera
                                </button>
                                <button type="button" id="takePhoto"
                                        class="hidden bg-orange-500 hover:bg-orange-600 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Capture
                                </button>
                                <button type="button" id="retakePhoto"
                                        class="hidden bg-gray-500 hover:bg-gray-600 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Retake
                                </button>
                            </div>
                        </div>

                        {{-- OR Divider --}}
                        <div class="text-gray-400 text-sm">OR</div>

                        {{-- File Upload --}}
                        <div class="flex flex-col items-center">
                            <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*"
                                   class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                                          file:rounded-full file:border-0 file:text-sm file:font-semibold
                                          file:bg-green-50 file:text-green-700 hover:file:bg-green-100
                                          focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <p class="text-xs text-gray-500 mt-1">Upload JPG/PNG or capture live photo</p>
                        </div>
                    </div>

                    {{-- Preview existing image if editing --}}
                    <div id="previewContainer" class="mt-3 text-center">
                        @if($student->exists && $student->profile_picture_url)
                            <div>
                                <span class="text-xs text-gray-600">Current Picture:</span>
                                <img src="{{ asset('storage/' . $student->profile_picture_url) }}"
                                     alt="Current Profile Picture"
                                     class="mt-1 w-20 h-20 rounded-full border object-cover mx-auto">
                            </div>
                        @endif
                    </div>

                    {{-- Hidden field for base64 data --}}
                    <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
                </div>

                {{-- Full Name --}}
                <div class="md:col-span-2 relative" x-data="{ inputValue: '{{ old('name', $student->name) }}' }">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out {{ $errors->has('name') ? 'border-red-500' : '' }}"
                           placeholder="e.g., John Doe"
                           value="{{ old('name', $student->name) }}"
                           maxlength="255"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'text', 3, 255)">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue.length > 0 ? (inputValue.length >= 3 ? 'text-green-500' : 'text-red-500') : ''">
                        <i class="fa-solid" :class="inputValue.length > 0 ? (inputValue.length >= 3 ? 'fa-circle-check' : 'fa-circle-xmark') : ''"></i>
                    </span>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Batch Dropdown --}}
                <div class="relative">
                    <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <select name="batch_id" id="batch_id"
                            class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out appearance-none pr-10 {{ $errors->has('batch_id') ? 'border-red-500' : '' }}">
                        <option value="">Select a Batch (Optional)</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('batch_id', $student->batch_id) == $batch->id ? 'selected' : '' }}>
                                {{ $batch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('batch_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Class Dropdown --}}
                <div class="relative" x-data="{ inputValue: '{{ old('class', $student->class) }}' }">
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select name="class" id="class"
                            class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out appearance-none pr-10 {{ $errors->has('class') ? 'border-red-500' : '' }}"
                            x-model="inputValue"
                            required
                            @change="validateInput($event.target, 'select')">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ old('class', $student->class) == $class ? 'selected' : '' }}>Class {{ $class }}</option>
                        @endforeach
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue ? 'text-green-500' : (inputValue.length > 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue ? 'fa-circle-check' : (inputValue.length > 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    @error('class')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mobile No --}}
                <div class="relative" x-data="{ inputValue: '{{ old('phone', $student->phone) }}' }">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile No (10 digits) <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="phone"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                           placeholder="e.g., 9876543210"
                           value="{{ old('phone', $student->phone) }}"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'phone')">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue.length === 10 ? 'text-green-500' : (inputValue.length > 0 && inputValue.length !== 10 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue.length === 10 ? 'fa-circle-check' : (inputValue.length > 0 && inputValue.length !== 10 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Fees --}}
                <div class="relative" x-data="{ inputValue: '{{ old('fees', $student->fees) }}' }">
                    <label for="fees" class="block text-sm font-medium text-gray-700 mb-1">Fees per month (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="fees" id="fees"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out {{ $errors->has('fees') ? 'border-red-500' : '' }}"
                           placeholder="e.g., 1500"
                           value="{{ old('fees', $student->fees) }}"
                           min="0"
                           step="0.01"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'number', 0)">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue > 0 ? 'text-green-500' : (inputValue.length > 0 && inputValue <= 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue > 0 ? 'fa-circle-check' : (inputValue.length > 0 && inputValue <= 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    @error('fees')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Joining Date --}}
                <div class="md:col-span-2 relative" x-data="{ inputValue: '{{ old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d')) }}' }">
                    <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date <span class="text-red-500">*</span></label>
                    <input type="date" name="join_date" id="join_date"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out {{ $errors->has('join_date') ? 'border-red-500' : '' }}"
                           value="{{ old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'date')">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue ? 'text-green-500' : (inputValue.length > 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue ? 'fa-circle-check' : (inputValue.length > 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    @error('join_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Roll No (Read-only if exists) --}}
                @if($student->exists)
                    <div class="md:col-span-2 relative">
                        <label for="roll_no" class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                        <input type="text" name="roll_no" id="roll_no"
                               value="{{ old('roll_no', $student->roll_no) }}"
                               class="block w-full px-4 py-2 text-base bg-gray-100 text-gray-600 cursor-not-allowed border border-gray-300 rounded-md shadow-sm"
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">Roll No is auto-generated and cannot be changed.</p>
                    </div>
                @endif

            </div>


            </div>

            {{-- Form Actions --}}
            <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end gap-4">
                <a href="{{ route('students.index') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                    @if($student->exists)
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Update Student</span>
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Save Student</span>
                    @endif
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('studentForm', () => ({
        init() {
            this.$nextTick(() => {
                this.$el.querySelectorAll('input, select').forEach(input => {
                    let type = '';
                    if (input.id === 'name') type = 'text';
                    else if (input.id === 'phone') type = 'phone';
                    else if (input.id === 'fees') type = 'number';
                    else if (input.id === 'join_date') type = 'date';
                    else if (input.id === 'class') type = 'select';

                    if (type && input.value) {
                        const min = input.hasAttribute('min') ? parseFloat(input.getAttribute('min')) : 0;
                        const max = input.hasAttribute('maxlength') ? parseFloat(input.getAttribute('maxlength')) : 255;
                        this.validateInput(input, type, min, max);
                    }
                });
            });
        },
        validateInput(inputElement, type, min = null, max = null) {
            const value = inputElement.value;
            let isValid = false;

            if (type === 'text') isValid = value.length >= min && value.length <= max;
            else if (type === 'phone') isValid = /^[0-9]{10}$/.test(value);
            else if (type === 'number') isValid = parseFloat(value) >= min;
            else if (type === 'date') isValid = value !== '';
            else if (type === 'select') isValid = value !== '';

            const parentGroup = inputElement.closest('.relative');
            if (parentGroup) {
                if (isValid) {
                    parentGroup.classList.add('is-valid');
                    parentGroup.classList.remove('is-invalid');
                } else {
                    parentGroup.classList.add('is-invalid');
                    parentGroup.classList.remove('is-valid');
                }
            }
        }
    }));
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const startBtn = document.getElementById("startCamera");
    const takeBtn = document.getElementById("takePhoto");
    const retakeBtn = document.getElementById("retakePhoto");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("snapshot");
    const placeholder = document.getElementById("placeholderText");
    const base64Input = document.getElementById("profile_picture_base64");
    let stream = null;

    startBtn.addEventListener("click", async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.classList.remove("hidden");
            placeholder.classList.add("hidden");
            takeBtn.classList.remove("hidden");
            startBtn.classList.add("hidden");
        } catch (err) {
            alert("Camera access denied or not available.");
        }
    });

    takeBtn.addEventListener("click", () => {
        const ctx = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.classList.remove("hidden");
        video.classList.add("hidden");
        takeBtn.classList.add("hidden");
        retakeBtn.classList.remove("hidden");

        const imageData = canvas.toDataURL("image/png");
        base64Input.value = imageData;
    });

    retakeBtn.addEventListener("click", () => {
        canvas.classList.add("hidden");
        video.classList.remove("hidden");
        retakeBtn.classList.add("hidden");
        takeBtn.classList.remove("hidden");
    });
});
</script>
@endpush
