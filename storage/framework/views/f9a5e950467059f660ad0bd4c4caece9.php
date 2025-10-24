

<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('header_title', 'Application Settings'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-3xl mx-auto">

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="<?php echo e(route('settings.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <fieldset class="mb-8">
                    <legend class="text-xl font-semibold text-gray-800 border-b-2 border-gray-200 pb-2 mb-4">Institute Details</legend>
                    <div class="space-y-4">
                        <div>
                            <label for="institute_name" class="block text-sm font-medium text-gray-700 mb-1">Institute Name</label>
                            <input type="text" name="institute_name" id="institute_name" 
                                   value="<?php echo e(old('institute_name', setting('institute_name', 'My Tuition Center'))); ?>" 
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label for="institute_logo_url" class="block text-sm font-medium text-gray-700 mb-1">Institute Logo URL</label>
                            <input type="url" name="institute_logo_url" id="institute_logo_url" 
                                   value="<?php echo e(old('institute_logo_url', setting('institute_logo_url'))); ?>" 
                                   placeholder="https://example.com/logo.png"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        
                    </div>
                </fieldset>

                <fieldset class="mb-8">
                    <legend class="text-xl font-semibold text-gray-800 border-b-2 border-gray-200 pb-2 mb-4">Academic Settings</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="session_year" class="block text-sm font-medium text-gray-700 mb-1">Current Session Year</label>
                            <input type="text" name="session_year" id="session_year" 
                                   value="<?php echo e(old('session_year', setting('session_year', date('Y') . '-' . (date('y') + 1)))); ?>" 
                                   placeholder="e.g., 2025-26"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label for="class_from" class="block text-sm font-medium text-gray-700 mb-1">Class (From)</label>
                            <input type="number" name="class_from" id="class_from" 
                                   value="<?php echo e(old('class_from', setting('class_from', 6))); ?>" 
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label for="class_to" class="block text-sm font-medium text-gray-700 mb-1">Class (To)</label>
                            <input type="number" name="class_to" id="class_to" 
                                   value="<?php echo e(old('class_to', setting('class_to', 12))); ?>" 
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                </fieldset>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="btn-start text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 text-lg">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/settings/index.blade.php ENDPATH**/ ?>