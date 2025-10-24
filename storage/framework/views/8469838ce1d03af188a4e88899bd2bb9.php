

<?php $__env->startSection('title', 'Mark Attendance'); ?>
<?php $__env->startSection('header_title', 'Mark Attendance for ' . $today); ?>

<?php $__env->startSection('content'); ?>
    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- CHANGED: Filter Form now filters by BATCH -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <form method="GET" action="<?php echo e(route('attendance.index')); ?>" class="flex items-center gap-4">
            <label for="batch_filter" class="font-semibold text-gray-700">Select Batch:</label>
            <select name="batch_filter" id="batch_filter" onchange="this.form.submit()" class="block w-auto bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($batch->id); ?>" <?php echo e($selectedBatchId == $batch->id ? 'selected' : ''); ?>><?php echo e($batch->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option value="">No batches created yet</option>
                <?php endif; ?>
            </select>
        </form>
    </div>

    <!-- Attendance Form -->
    <form action="<?php echo e(route('attendance.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="date" value="<?php echo e(\Carbon\Carbon::today()->toDateString()); ?>">
        
        <div class="flex items-center gap-4 mb-4">
            <button type="button" id="markAllPresent" class="btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300">
                Mark All Present
            </button>
            <button type="button" id="markAllAbsent" class="btn-stop text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                Mark All Absent
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 md:px-6 text-left">Roll No</th>
                            <th class="py-3 px-4 md:px-6 text-left">Name</th>
                            <th class="py-3 px-4 md:px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $attendanceList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap"><?php echo e($student['roll_no']); ?></td>
                                <td class="py-3 px-4 md:px-6 text-left font-semibold"><?php echo e($student['name']); ?></td>
                                <td class="py-3 px-4 md:px-6 text-center">
                                    <div class="attendance-toggle inline-flex shadow-sm rounded-md" role="group">
                                        <input type="radio" id="present_<?php echo e($student['id']); ?>" name="attendance[<?php echo e($student['id']); ?>]" value="present" class="attendance-radio" <?php echo e($student['status'] == 'present' ? 'checked' : ''); ?> required>
                                        <label for="present_<?php echo e($student['id']); ?>" class="label-present">Present</label>
                                        
                                        <input type="radio" id="absent_<?php echo e($student['id']); ?>" name="attendance[<?php echo e($student['id']); ?>]" value="absent" class="attendance-radio" <?php echo e($student['status'] == 'absent' ? 'checked' : ''); ?> required>
                                        <label for="absent_<?php echo e($student['id']); ?>" class="label-absent">Absent</label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="py-6 px-4 text-center text-gray-500">No students found for this batch.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-6 text-right">
            <button type="submit" class="btn-start text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 text-lg">
                Save Attendance
            </button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const markAllPresentButton = document.getElementById('markAllPresent');
        const markAllAbsentButton = document.getElementById('markAllAbsent');
        
        const presentRadios = document.querySelectorAll('input[type="radio"][value="present"]');
        const absentRadios = document.querySelectorAll('input[type="radio"][value="absent"]');

        markAllPresentButton.addEventListener('click', () => {
            presentRadios.forEach(radio => {
                radio.checked = true;
            });
        });

        markAllAbsentButton.addEventListener('click', () => {
            absentRadios.forEach(radio => {
                radio.checked = true;
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/attendance/index.blade.php ENDPATH**/ ?>