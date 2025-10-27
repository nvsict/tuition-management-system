@extends('layouts.app')

@section('title', 'Fee Management')
@section('header_title', 'Fee Management & Ledger')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops! Please fix the errors:</strong>
            <ul class="list-disc ml-6 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Stat Card (Total Due) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-orange-600 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold">Total Due (All Students)</h2>
            <p class="text-4xl font-light">₹{{ number_format($totalDue, 2) }}</p>
        </div>
    </div>


    <!-- Main Ledger Layout (2 columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Actions (Select Student, Add Payment) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Student Selector -->
            <div class="bg-white rounded-xl shadow-xl p-6 transition duration-300 transform hover:shadow-2xl">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Select Student</h2>
                
                <form method="GET" action="{{ route('fees.index') }}">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student:</label>
                    <select name="student_id" id="student_id_select" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" onchange="this.form.submit()">
                        <option value="">-- Select a student --</option>
                        
                        @foreach($masterStudentList as $student)
                        <option 
                            value="{{ $student->id }}" 
                            {{ ($selectedStudent && $selectedStudent->id == $student->id) ? 'selected' : '' }}
                            data-fee="{{ $student->fees }}"
                            {{-- Removed conditional class --}}
                        >
                            {{ $student->name }} ({{ $student->batch->name ?? 'No Batch' }})
                            {{-- Removed (Due: ₹...) --}}
                        </option>
                    @endforeach
                    </select>
                </form>
            </div>

            <!-- Record Payment Form -->
            @if($selectedStudent)
                <div class="bg-white rounded-xl shadow-xl p-6 transition duration-300 transform hover:shadow-2xl">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Record Payment</h2>
                    <form action="{{ route('fees.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₹)</label>
                                <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" min="0.01" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                <p id="monthly-fee-hint" class="text-xs text-gray-500 mt-1">
                                    Monthly Fee: ₹{{ number_format($selectedStudent->fees, 2) }}
                                </p>
                            </div>
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <input type="text" name="description" id="description" value="{{ old('description', 'Payment') }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="e.g., Cash, UPI, Partial">
                            </div>
                        </div>
                        
                        <div class="mt-6 text-right">
                            <button type="submit" class="btn-start w-full text-white font-bold py-3 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Right Column: Ledger -->
        <div class="lg:col-span-2">
            @if($selectedStudent)
                @php
                    $cycle_count = abs(round($currentBalance / $selectedStudent->fees));
                    $status_class = 'bg-gray-100 border-gray-300';
                    $status_text = 'Paid Up';
                    if ($currentBalance >= $selectedStudent->fees) {
                        $status_class = 'bg-red-100 border-red-500 animate-pulse';
                        $status_text = 'Behind ' . $cycle_count . ' Cycle(s)';
                    } elseif ($currentBalance > 0) {
                        $status_class = 'bg-orange-100 border-orange-500';
                        $status_text = 'Partial Payment Due';
                    } elseif ($currentBalance < 0) {
                        $status_class = 'bg-blue-100 border-blue-500';
                        $status_text = 'Credit Balance (Paid Ahead)';
                    }
                @endphp

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $selectedStudent->name }}'s Ledger</h2>

                    <!-- Current Balance -->
                    <div class="mb-6 p-4 rounded-xl border-4 {{ $status_class }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Current Balance</label>
                                @php $balanceColor = $currentBalance > 0 ? 'text-red-700' : 'text-green-700'; @endphp
                                <p class="text-4xl font-bold {{ $balanceColor }}">
                                    ₹{{ number_format(abs($currentBalance), 2) }}
                                    <span class="text-2xl font-medium">{{ $currentBalance > 0 ? 'Due' : 'Credit' }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold uppercase text-gray-700">Status:</span>
                                <p class="text-xl font-extrabold {{ $currentBalance > 0 ? 'text-red-700' : 'text-green-700' }}">{{ $status_text }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Transaction History</h3>
                    <div class="overflow-x-auto border rounded-xl">
                        <table class="min-w-full leading-normal">
                            <thead class="sticky top-0 bg-gray-100 z-10 shadow-sm">
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
                                    <th class="py-3 px-4 text-left">Date</th>
                                    <th class="py-3 px-4 text-left">Description</th>
                                    <th class="py-3 px-4 text-right">Amount (₹)</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse ($transactions as $tx)
                                    @php
                                        $row_bg = $tx->amount > 0 ? 'bg-green-50/50' : 'bg-red-50/50';
                                    @endphp
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 {{ $row_bg }}">
                                        <td class="py-3 px-4 text-left">{{ $tx->date->format('d-M-Y') }}</td>
                                        <td class="py-3 px-4 text-left">{{ $tx->description }}</td>
                                        <td class="py-3 px-4 text-right font-semibold {{ $tx->amount > 0 ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <button type="button" 
                                                    onclick="editTransaction({{ $tx->id }}, '{{ addslashes($tx->description) }}', {{ abs($tx->amount) }}, '{{ $tx->date->format('Y-m-d') }}', {{ $tx->amount > 0 ? 'true' : 'false' }})"
                                                    class="text-blue-500 hover:text-blue-800 text-xs mr-2">
                                                Edit
                                            </button>
                                            <button type="button" 
                                                    onclick="confirmDelete('{{ route('fees.destroy', $tx) }}')" 
                                                    class="text-red-500 hover:text-red-800 text-xs">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 px-4 text-center text-gray-500">No transactions found for this student.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 flex flex-col items-center justify-center min-h-[300px] text-center">
                    <p class="text-xl text-gray-500">Please select a student to view their fee ledger.</p>
                    <p class="text-md text-gray-400 mt-2">You can also click on the "Dues This Week" card on the Dashboard to get started quickly.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL FOR EDITING TRANSACTIONS -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Edit Transaction</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="description" id="edit_description" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    <div>
                        <label for="edit_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                        <input type="number" name="amount" id="edit_amount" min="0.01" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                        <p id="edit-hint" class="text-xs text-gray-500 mt-1"></p>
                    </div>
                    <div>
                        <label for="edit_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" id="edit_date" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-4">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="btn-stop text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </button>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
    function confirmDelete(deleteUrl) {
        if (confirm('Are you sure you want to delete this transaction? This cannot be undone.')) {
            let form = document.getElementById('deleteForm');
            if (!form) {
                form = document.createElement('form');
                form.id = 'deleteForm';
                form.method = 'POST';
                form.style.display = 'none';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
            }
            
            form.action = deleteUrl;
            form.submit();
        }
    }

    function editTransaction(id, description, amount, date, isCharge) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        const hint = document.getElementById('edit-hint');
        
        form.action = '/fees/' + id; 
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_amount').value = amount;
        document.getElementById('edit_date').value = date;

        if (isCharge) {
            hint.textContent = 'This is a CHARGE transaction (invoice). Changing the amount will affect the balance.';
            hint.className = 'text-xs text-green-600 mt-1';
        } else {
            hint.textContent = 'This is a PAYMENT transaction. Changing the amount will affect the balance.';
            hint.className = 'text-xs text-red-600 mt-1';
        }
        
        modal.classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_id_select');
        const monthlyFeeHint = document.getElementById('monthly-fee-hint');

        if (monthlyFeeHint) {
            studentSelect.addEventListener('change', function() {
                const selectedOption = studentSelect.options[studentSelect.selectedIndex];
                if (selectedOption.value) {
                    const monthlyFee = selectedOption.dataset.fee;
                    monthlyFeeHint.textContent = 'Monthly Fee: ₹' + parseFloat(monthlyFee).toFixed(2);
                }
            });

            const initialOption = studentSelect.options[studentSelect.selectedIndex];
            if (initialOption && initialOption.value) {
                const initialFee = initialOption.dataset.fee;
                monthlyFeeHint.textContent = 'Monthly Fee: ₹' + parseFloat(initialFee).toFixed(2);
            }
        }
    });
</script>
@endpush
@endsection
