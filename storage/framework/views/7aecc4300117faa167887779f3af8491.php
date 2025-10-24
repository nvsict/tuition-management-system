

<?php $__env->startSection('title', 'Attendance Report'); ?>
<?php $__env->startSection('header_title', 'Attendance Report'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Filter Report</h2>
        <form method="GET" action="<?php echo e(route('attendance.report')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- CHANGED: Batch Filter -->
            <div>
                <label for="batch_filter" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                <select name="batch_filter" id="batch_filter" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Batches</option>
                    <?php $__currentLoopData = $allBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($batch->id); ?>" <?php echo e($filters['selectedBatch'] == $batch->id ? 'selected' : ''); ?>><?php echo e($batch->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Student Filter (no change) -->
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <select name="student_id" id="student_id" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Students</option>
                    <?php $__currentLoopData = $allStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($student->id); ?>" <?php echo e($filters['selectedStudent'] == $student->id ? 'selected' : ''); ?>>
                            <?php echo e($student->name); ?> (Class <?php echo e($student->class); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo e($filters['startDate']); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo e($filters['endDate']); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Buttons -->
            <div class="col-span-1 md:col-span-2 lg:col-span-4 flex justify-end gap-4 mt-4">
                <a href="<?php echo e(route('attendance.export', request()->query())); ?>" 
                   class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                    Export to CSV
                </a>
                <a href="<?php echo e(route('attendance.report')); ?>" class="btn-stop text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-300">
                    Clear Filters
                </a>
                <button type="submit" class="btn-start text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-green-700 transition duration-300">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Student Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th> <!-- ADDED -->
                        <th class="py-3 px-4 md:px-6 text-left">Class</th>
                        <th class="py-3 px-4 md:px-6 text-center">Total Days</th>
                        <th class="py-3 px-4 md:px-6 text-center">Present</th>
                        <th class="py-3 px-4 md:px-6 text-center">Absent</th>
                        <th class="py-3 px-4 md:px-6 text-center">Present %</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap font-semibold"><?php echo e($data->name); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($data->batch_name); ?></td> <!-- ADDED -->
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($data->class); ?></td>
                            <td class="py-3 px-4 md:px-6 text-center font-bold"><?php echo e($data->total_days); ?></td>
                            <td class="py-3 px-4 md:px-6 text-center text-green-700"><?php echo e($data->total_present); ?></td>
                            <td class="py-3 px-4 md:px-6 text-center text-red-700"><?php echo e($data->total_absent); ?></td>

                            <?php
                                $percent_class = 'text-gray-500';
                                if ($data->present_percentage >= 80) {
                                    $percent_class = 'text-green-700';
                                } elseif ($data->present_percentage >= 50) {
                                    $percent_class = 'text-orange-600';
                                } elseif ($data->total_days > 0) {
                                    $percent_class = 'text-red-700';
                                }
                            ?>
                            
                            <td class="py-3 px-4 md:px-6 text-center font-bold text-lg <?php echo e($percent_class); ?>">
                                <?php echo e($data->present_percentage); ?>%
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-6 px-4 text-center text-gray-500">No attendance data found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/attendance/report.blade.php ENDPATH**/ ?>