

<?php $__env->startSection('title', $student->exists ? 'Edit Student' : 'Add Student'); ?>
<?php $__env->startSection('header_title', $student->exists ? 'Edit Student' : 'Add New Student'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-2xl mx-auto">
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
        
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="<?php echo e($student->exists ? route('students.update', $student) : route('students.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($student->exists): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name', $student->name)); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <!-- BATCH DROPDOWN (NEW) -->
                    <div>
                        <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                        <select name="batch_id" id="batch_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select a Batch (Optional)</option>
                            <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($batch->id); ?>" <?php echo e(old('batch_id', $student->batch_id) == $batch->id ? 'selected' : ''); ?>>
                                    <?php echo e($batch->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- CLASS DROPDOWN (Now more of a metadata field) -->
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class (e.g., 10, 11, 12)</label>
                        <select name="class" id="class" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select Class</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class); ?>" <?php echo e(old('class', $student->class) == $class ? 'selected' : ''); ?>>
                                    Class <?php echo e($class); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile No (10 digits)</label>
                        <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone', $student->phone)); ?>" pattern="[0-9]{10}" title="Please enter a 10-digit mobile number" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div>
                        <label for="fees" class="block text-sm font-medium text-gray-700 mb-1">Fees per month (₹)</label>
                        <input type="number" name="fees" id="fees" value="<?php echo e(old('fees', $student->fees)); ?>" min="0" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
                        <input type="date" name="join_date" id="join_date" value="<?php echo e(old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d'))); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <?php if($student->exists): ?>
                        <div class="md:col-span-2">
                            <label for="roll_no" class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                            <input type="text" name="roll_no" id="roll_no" value="<?php echo e(old('roll_no', $student->roll_no)); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100" readonly>
                            <p class="text-xs text-gray-500 mt-1">Roll No is auto-generated and cannot be changed.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-8 flex justify-end gap-4">
                    <a href="<?php echo e(route('students.index')); ?>" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        <?php echo e($student->exists ? 'Update Student' : 'Save Student'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/students/form.blade.php ENDPATH**/ ?>