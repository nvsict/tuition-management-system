@extends('layouts.app')

@section('title', 'Fee Reminders')
@section('header_title', 'Fee Reminder List')

@section('content')

    <!-- Reminder Summary and Filter -->
    <div class="bg-white rounded-xl shadow-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            
            <!-- Overall Status -->
            <p class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">
                Total Students Overdue: <span class="text-red-600">{{ $studentsWithDues->count() }}</span>
            </p>

            <!-- Filter by Batch -->
            <form id="filterForm" method="GET" action="{{ route('fees.reminders') }}">
                @php
                    // We need to fetch batches here for the filter, as this page only gets studentsWithDues
                    $allBatches = \App\Models\Batch::orderBy('name')->get();
                    $selectedBatchId = request('batch_filter');
                @endphp
                <label for="batch_filter" class="text-sm font-medium text-gray-700 mr-2">Filter by Batch:</label>
                <select name="batch_filter" id="batch_filter" onchange="this.form.submit()" class="inline-block border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Batches</option>
                    @foreach($allBatches as $batch)
                        <option value="{{ $batch->id }}" {{ $selectedBatchId == $batch->id ? 'selected' : '' }}>
                            {{ $batch->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    
    <!-- Table and Bulk Action -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- NEW: Bulk Action Bar (Hidden by default) -->
        <div id="bulk-action-bar" class="hidden bg-blue-50 p-3 border-b-2 border-blue-200 flex justify-between items-center transition duration-300">
             <span id="selected-count" class="text-blue-700 font-semibold">0 students selected</span>
             <button id="bulkRemindButton" 
                     class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                 <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M14.003 11.237a.749.749 0 00-.53-.217c-.187 0-.365.074-.5.209l-.89 1.018a.75.75 0 01-1.127.051l-2.028-1.574a.75.75 0 01-.192-1.258l.89-1.018a.75.75 0 00-.05-1.127l-1.574-2.028a.75.75 0 00-1.258-.192l-1.018.89a.75.75 0 00-.209.5l-.218.531c-.134.33-.035.7.275.986l4.67 4.203c.33.296.757.37 1.137.197l.53-.217a.748.748 0 00.5-.209l1.018-.89a.75.75 0 00.051-1.127l-1.574-2.028a.75.75 0 00-1.127-.051l-1.018.89c-.066.075-.16.117-.258.117a.36.36 0 01-.258-.117l-3.34-2.997a.36.36 0 01-.117-.258c0-.1.042-.192.117-.258l.89-1.018a.36.36 0 01.258-.117c.1 0 .192.042.258.117l1.309 1.69a.36.36 0 01.117.258.75.75 0 00.94.577l2.028-1.574a.75.75 0 011.258.192l1.018.89c.134.117.217.28.217.45 0 .17-.083.333-.217.45z"/></svg>
                 Send Bulk Reminder
             </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal sticky top-0">
                        <th class="py-3 px-4 md:px-6 text-left w-12">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                        </th>
                        <th class="py-3 px-4 md:px-6 text-left">Student Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th>
                        <th class="py-3 px-4 md:px-6 text-left">Contact Info</th>
                        <th class="py-3 px-4 md:px-6 text-right">Amount Due (₹)</th>
                        <th class="py-3 px-4 md:px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($studentsWithDues as $student)
                        @php
                            // Filter students by batch in the view if a filter is set
                            if ($selectedBatchId && $student->batch_id != $selectedBatchId) {
                                continue;
                            }
                        @endphp
                        <tr class="border-b border-gray-200 hover:bg-red-50" data-phone="{{ $student->phone }}" data-name="{{ $student->name }}" data-due="{{ number_format($student->transactions_sum_amount, 2) }}">
                            <td class="py-3 px-4 md:px-6 text-left w-12">
                                <input type="checkbox" name="selected_student[]" value="{{ $student->id }}" class="row-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                            </td>
                            <td class="py-3 px-4 md:px-6 text-left font-semibold">
                                <a href="{{ route('fees.index', ['student_id' => $student->id]) }}" class="text-blue-600 hover:underline">
                                    {{ $student->name }}
                                </a>
                            </td>
                            <td class="py-3 px-4 md:px-6 text-left">{{ $student->batch->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 md:px-6 text-left">
                                <span class="font-mono text-gray-600">{{ $student->phone }}</span>
                            </td>
                            <td class="py-3 px-4 md:px-6 text-right font-bold text-red-700">
                                ₹{{ number_format($student->transactions_sum_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 md:px-6 text-center">
                                @php
                                    $instituteName = setting('institute_name', 'our tuition class');
                                    $encodedMessage = urlencode("Dear Parent,\n\nThis is a friendly reminder from {$instituteName} regarding the outstanding fee balance for {$student->name}.\n\nAmount Due: ₹{$student->transactions_sum_amount}.\n\nWe kindly request you to clear the dues at your earliest convenience.\n\nThank you.");
                                    $whatsAppUrl = "https://wa.me/91{$student->phone}?text={$encodedMessage}";
                                @endphp

                                <a href="{{ $whatsAppUrl }}" 
                                   target="_blank"
                                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1 px-3 rounded-full transition duration-300">
                                   <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M14.003 11.237a.749.749 0 00-.53-.217c-.187 0-.365.074-.5.209l-.89 1.018a.75.75 0 01-1.127.051l-2.028-1.574a.75.75 0 01-.192-1.258l.89-1.018a.75.75 0 00-.05-1.127l-1.574-2.028a.75.75 0 00-1.258-.192l-1.018.89a.75.75 0 00-.209.5l-.218.531c-.134.33-.035.7.275.986l4.67 4.203c.33.296.757.37 1.137.197l.53-.217a.748.748 0 00.5-.209l1.018-.89a.75.75 0 00.051-1.127l-1.574-2.028a.75.75 0 00-1.127-.051l-1.018.89c-.066.075-.16.117-.258.117a.36.36 0 01-.258-.117l-3.34-2.997a.36.36 0 01-.117-.258c0-.1.042-.192.117-.258l.89-1.018a.36.36 0 01.258-.117c.1 0 .192.042.258.117l1.309 1.69a.36.36 0 01.117.258.75.75 0 00.94.577l2.028-1.574a.75.75 0 011.258.192l1.018.89c.134.117.217.28.217.45 0 .17-.083.333-.217.45z"/></svg>
                                   Remind
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 px-4 text-center text-gray-500">
                                <div class="text-2xl">🎉</div>
                                <p class="mt-2 font-semibold">No students have outstanding dues!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkActionBar = document.getElementById('bulk-action-bar');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkRemindButton = document.getElementById('bulkRemindButton');

        function updateActionBar() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            selectedCountSpan.textContent = `${checkedCount} student(s) selected`;

            if (checkedCount > 0) {
                bulkActionBar.classList.remove('hidden');
            } else {
                bulkActionBar.classList.add('hidden');
            }
        }

        // Select All functionality
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateActionBar();
        });

        // Individual checkbox functionality
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateActionBar);
        });
        
        // Bulk Remind Button Action (The Smart Logic)
        bulkRemindButton.addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('.row-checkbox:checked');
            const parentDues = {}; // Key: Phone Number, Value: Array of student names

            selectedRows.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const phone = row.dataset.phone;
                const name = row.dataset.name;
                const due = row.dataset.due;

                if (!parentDues[phone]) {
                    parentDues[phone] = [];
                }
                parentDues[phone].push({ name, due });
            });

            if (Object.keys(parentDues).length === 0) {
                alert("Please select at least one student to send a bulk reminder.");
                return;
            }
            
            // Build the master message and open in a new tab
            let fullMessage = "Dear Parent,\n\nThis is a consolidated friendly reminder from {{ setting('institute_name', 'our tuition class') }} regarding the following outstanding fee balances:\n\n";
            let totalBulkDue = 0;
            let phoneNumbers = []; // Store unique phone numbers

            for (const phone in parentDues) {
                phoneNumbers.push(phone); // Add phone number to list
                const studentsInGroup = parentDues[phone];
                
                // Add contact group header
                fullMessage += "--- Contact: " + phone + " ---\n";
                
                // List all students under this contact
                studentsInGroup.forEach(student => {
                    fullMessage += ` - ${student.name}: ₹${student.due}\n`;
                    totalBulkDue += parseFloat(student.due.replace(/,/g, '')); // Sum the amount
                });
            }
            
            fullMessage += "\nTotal Consolidated Due: ₹" + totalBulkDue.toLocaleString('en-IN') + "\n\n";
            fullMessage += "We kindly request you to clear the dues at your earliest convenience.\n\nThank you.";
            
            // For bulk, we can only open the first unique number due to browser limitations
            const firstPhone = phoneNumbers[0];
            const countryCode = '91';

            const encodedMessage = encodeURIComponent(fullMessage);
            const whatsAppUrl = `https://wa.me/${countryCode}${firstPhone}?text=${encodedMessage}`;
            
            if (phoneNumbers.length > 1) {
                 alert("Warning: Multiple unique parent contacts were selected. We can only open one WhatsApp chat at a time. The chat for the first contact (" + firstPhone + ") will open. You will need to manually open chats for the other contacts.");
            }

            window.open(whatsAppUrl, '_blank');
            
            // You might want to automatically uncheck everything after sending
            rowCheckboxes.forEach(checkbox => checkbox.checked = false);
            updateActionBar(); 
        });
        
        // Initial setup to ensure the filter works correctly if batch_filter is in the URL
        updateActionBar();
    });
</script>
@endpush