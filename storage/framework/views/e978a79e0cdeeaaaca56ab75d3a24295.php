

<?php $__env->startSection('title', 'Student Management'); ?>
<?php $__env->startSection('header_title', 'Student Management'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <a href="<?php echo e(route('students.create')); ?>" class="w-full md:w-auto btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300 text-center">
            + Add New Student
        </a>
        
        <!-- UPDATED: Filter Form now uses Batches -->
        <form method="GET" action="<?php echo e(route('students.index')); ?>" class="w-full md:w-auto flex items-center gap-2">
            <select name="batch_filter" class="block w-full bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">All Batches</option>
                <?php $__currentLoopData = $allBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($batch->id); ?>" <?php echo e(request('batch_filter') == $batch->id ? 'selected' : ''); ?>>
                        <?php echo e($batch->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-gray-800 transition duration-300">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th> <!-- ADDED -->
                        <th class="py-3 px-4 md:px-6 text-left">Class</th>
                        <th class="py-3 px-4 md:px-6 text-left">Phone</th>
                        <th class="py-3 px-4 md:px-6 text-left">Fees</th>
                        <th class="py-3 px-4 md:px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer" 
                            data-href="<?php echo e(route('students.edit', $student)); ?>">
                            
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap"><?php echo e($student->name); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->batch->name ?? 'N/A'); ?></td> <!-- ADDED -->
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->class); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->phone); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left">₹<?php echo e(number_format($student->fees)); ?></td>
                            
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap">
                                <div class="flex gap-3 items-center">
                                    <a href="<?php echo e(route('fees.index', ['student_id' => $student->id])); ?>"
   onclick="event.stopPropagation()"
   class="btn-start text-white text-xs font-bold py-1 px-3 rounded-full shadow hover:bg-green-700 transition duration-300">
    Pay Fee
</a>
                                    <a href="<?php echo e(route('students.edit', $student)); ?>" 
                                       onclick="event.stopPropagation()"
                                       class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                    <form action="<?php echo e(route('students.destroy', $student)); ?>" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this student?');"
                                          onclick="event.stopPropagation()">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <!-- UPDATED: Colspan is now 7 -->
                            <td colspan="7" class="py-6 px-4 text-center text-gray-500">No students found. Add one to get started!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-white">
            <?php echo e($students->appends(request()->query())->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('tr[data-href]');
        
        rows.forEach(row => {
            row.addEventListener('click', () => {
                window.location.href = row.dataset.href;
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/students/index.blade.php ENDPATH**/ ?>