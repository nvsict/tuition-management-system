

<?php $__env->startSection('title', 'Fee Management'); ?>
<?php $__env->startSection('header_title', 'Fee Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-800">Fee Management</h1>
        <a href="<?php echo e(route('fees.create')); ?>" class="w-full md:w-auto btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300 text-center">
            + Add Fee Payment
        </a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-600 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold">Total Collected</h2>
            <p class="text-4xl font-light">₹<?php echo e(number_format($totalCollected, 2)); ?></p>
        </div>
        <div class="bg-orange-600 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold">Total Due</h2>
            <p class="text-4xl font-light">₹<?php echo e(number_format($totalDue, 2)); ?></p>
        </div>
    </div>

    <!-- Fees Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Student Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Month (For)</th>
                        <th class="py-3 px-4 md:px-6 text-left">Amount Paid</th>
                        <th class="py-3 px-4 md:px-6 text-left">Due Amount</th>
                        <th class="py-3 px-4 md:px-6 text-left">Payment Date</th>
                        <th class="py-3 px-4 md:px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    
                    <?php $__empty_1 = true; $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        
                        <?php
                            // THE FIX: This is a safe, native PHP comment.
                            // Determine row class based on due amount.
                            $row_class = ($fee->due_amount > 0)
                                ? 'bg-red-50 hover:bg-red-100'
                                : 'hover:bg-gray-50';
                        ?>

                        <tr class="border-b border-gray-200 <?php echo e($row_class); ?>">
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap font-semibold"><?php echo e($fee->student->name); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($fee->month); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left text-green-700 font-bold">₹<?php echo e(number_format($fee->amount_paid)); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left text-red-700 font-bold">₹<?php echo e(number_format($fee->due_amount)); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($fee->date->format('d-M-Y')); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left flex gap-2">
                                <a href="<?php echo e(route('fees.edit', $fee)); ?>" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                <form action="<?php echo e(route('fees.destroy', $fee)); ?>" method="POST" onsubmit="return confirm('Are you sure?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-6 px-4 text-center text-gray-500">No fee payments recorded yet.</td>
                        </tr>
                    
                    <?php endif; ?> <!-- Corrected typo -->
                
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 bg-white">
            <?php echo e($fees->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/fees/index.blade.php ENDPATH**/ ?>