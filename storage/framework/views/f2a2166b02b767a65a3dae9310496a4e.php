

<?php $__env->startSection('title', $batch->exists ? 'Edit Batch' : 'Add Batch'); ?>
<?php $__env->startSection('header_title', $batch->exists ? 'Edit Batch' : 'Add New Batch'); ?>

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
            <form action="<?php echo e($batch->exists ? route('batches.update', $batch) : route('batches.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($batch->exists): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Batch Name</label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name', $batch->name)); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required placeholder="e.g., Morning Batch (Class 10)">
                    </div>

                    <div>
                        <label for="time_slot" class="block text-sm font-medium text-gray-700 mb-1">Time Slot (Optional)</label>
                        <input type="text" name="time_slot" id="time_slot" value="<?php echo e(old('time_slot', $batch->time_slot)); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="e.g., 7:00 AM - 9:00 AM">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="<?php echo e(route('batches.index')); ?>" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                        <?php echo e($batch->exists ? 'Update Batch' : 'Save Batch'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/batches/form.blade.php ENDPATH**/ ?>