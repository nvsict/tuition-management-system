

<?php $__env->startSection('title', 'Add Fee Payment'); ?>
<?php $__env->startSection('header_title', 'Add Fee Payment'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-2xl mx-auto">
        <!-- Validation Errors -->
        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <ul class="list-disc ml-6 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- The Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="<?php echo e(route('fees.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Student Dropdown -->
                    <div class="md:col-span-2">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                        <select name="student_id" id="student_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select a student</option>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($student->id); ?>" <?php echo e(old('student_id') == $student->id ? 'selected' : ''); ?>>
                                    <?php echo e($student->name); ?> (Class <?php echo e($student->class); ?>) - Fee: ₹<?php echo e($student->fees); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Amount Paid -->
                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₹)</label>
                        <input type="number" name="amount_paid" id="amount_paid" value="<?php echo e(old('amount_paid')); ?>" min="0" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="date" name="date" id="date" value="<?php echo e(old('date', date('Y-m-d'))); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                    <!-- Month -->
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Payment For Month</label>
                        <select name="month" id="month" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month); ?>" <?php echo e(old('month', $currentMonth) == $month ? 'selected' : ''); ?>><?php echo e($month); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Payment For Year</label>
                        <input type="number" name="year" id="year" value="<?php echo e(old('year', $currentYear)); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end gap-4">
                    <a href="<?php echo e(route('fees.index')); ?>" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/fees/create.blade.php ENDPATH**/ ?>