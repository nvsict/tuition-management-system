

<?php $__env->startSection('title', 'Fee Management'); ?>
<?php $__env->startSection('header_title', 'Fee Management & Ledger'); ?>

<?php $__env->startSection('content'); ?>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops! Please fix the errors:</strong>
            <ul class="list-disc ml-6 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Stat Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-orange-600 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold">Total Due (All Students)</h2>
            <p class="text-4xl font-light">₹<?php echo e(number_format($totalDue, 2)); ?></p>
        </div>
    </div>

    <!-- Main Ledger Layout (2 columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Actions (Select Student, Add Payment) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Student Selector -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Select Student</h2>
                <form method="GET" action="<?php echo e(route('fees.index')); ?>">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select name="student_id" id="student_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" onchange="this.form.submit()">
                        <option value="">-- Select a student --</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($student->id); ?>" <?php echo e(($selectedStudent && $selectedStudent->id == $student->id) ? 'selected' : ''); ?>>
                                <?php echo e($student->name); ?> (<?php echo e($student->batch->name ?? 'No Batch'); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>

            <!-- Record Payment Form (Only show if a student is selected) -->
            <?php if($selectedStudent): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Record Payment</h2>
                    <form action="<?php echo e(route('fees.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="student_id" value="<?php echo e($selectedStudent->id); ?>">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₹)</label>
                                <input type="number" name="amount_paid" id="amount_paid" value="<?php echo e(old('amount_paid')); ?>" min="0.01" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            </div>
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                <input type="date" name="date" id="date" value="<?php echo e(old('date', date('Y-m-d'))); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <input type="text" name="description" id="description" value="<?php echo e(old('description', 'Payment')); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="e.g., Payment, Cash">
                            </div>
                        </div>
                        
                        <div class="mt-6 text-right">
                            <button type="submit" class="btn-start w-full text-white font-bold py-3 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Ledger (Balance, History) -->
        <div class="lg:col-span-2">
            <?php if($selectedStudent): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4"><?php echo e($selectedStudent->name); ?>'s Ledger</h2>
                    
                    <!-- Current Balance -->
                    <div class="mb-6 p-4 rounded-lg
                        <?php if($currentBalance > 0): ?> bg-red-100 border border-red-300 <?php else: ?> bg-green-100 border border-green-300 <?php endif; ?>
                    ">
                        <?php
                            $balanceText = $currentBalance > 0 ? 'Due' : 'Credit';
                            $balanceColor = $currentBalance > 0 ? 'text-red-700' : 'text-green-700';
                        ?>
                        <label class="block text-sm font-medium text-gray-600">Current Balance</label>
                        <p class="text-4xl font-bold <?php echo e($balanceColor); ?>">
                            ₹<?php echo e(number_format(abs($currentBalance), 2)); ?>

                            <span class="text-2xl font-medium"><?php echo e($balanceText); ?></span>
                        </p>
                    </div>

                    <!-- Transaction History -->
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Transaction History</h3>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
                                    <th class="py-3 px-4 text-left">Date</th>
                                    <th class="py-3 px-4 text-left">Description</th>
                                    <th class="py-3 px-4 text-right">Amount (₹)</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left"><?php echo e($tx->date->format('d-M-Y')); ?></td>
                                        <td class="py-3 px-4 text-left"><?php echo e($tx->description); ?></td>
                                        
                                        <!-- Color code the amount -->
                                        <?php if($tx->amount > 0): ?>
                                            <td class="py-3 px-4 text-right font-semibold text-green-700">+<?php echo e(number_format($tx->amount, 2)); ?></td>
                                        <?php else: ?>
                                            <td class="py-3 px-4 text-right font-semibold text-red-700"><?php echo e(number_format($tx->amount, 2)); ?></td>
                                        <?php endif; ?>
                                        
                                        <td class="py-3 px-4 text-center">
                                            <form action="<?php echo e(route('fees.destroy', $tx)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction? This cannot be undone.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" 
        onclick="confirmDelete('<?php echo e(route('fees.destroy', $tx)); ?>')" 
        class="text-red-500 hover:text-red-800 text-xs">
    Delete
</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="py-6 px-4 text-center text-gray-500">No transactions found for this student.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State: No student selected -->
                <div class="bg-white rounded-lg shadow-lg p-12 flex items-center justify-center min-h-[300px]">
                    <p class="text-xl text-gray-500">
                        Please select a student to view their fee ledger.
                    </p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(deleteUrl) {
        if (confirm('Are you sure you want to delete this transaction? This cannot be undone.')) {
            // Create a hidden form if it doesn't exist
            let form = document.getElementById('deleteForm');
            if (!form) {
                form = document.createElement('form');
                form.id = 'deleteForm';
                form.method = 'POST';
                form.style.display = 'none'; // Keep it hidden

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '<?php echo e(csrf_token()); ?>'; // Add CSRF token
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE'; // Specify DELETE method
                form.appendChild(methodInput);

                document.body.appendChild(form); // Add form to the page
            }

            // Set the form's action URL and submit it
            form.action = deleteUrl;
            form.submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/transactions/index.blade.php ENDPATH**/ ?>