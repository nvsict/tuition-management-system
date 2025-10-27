

<?php $__env->startSection('title', 'Student Management'); ?>
<?php $__env->startSection('header_title', 'Student Management'); ?>

<?php $__env->startSection('content'); ?>
    
    <style>
        .table-header {
            background-color: #f3f4f6; /* Lighter gray for header */
            color: #4b5563; /* Darker gray for text */
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 600; /* Semi-bold */
            letter-spacing: 0.05em;
        }
        .table-row-hover:hover {
            background-color: #f9fafb; /* Very light gray on hover */
            cursor: pointer;
        }
        .action-button {
            padding: 0.3rem 0.75rem; /* Smaller padding for actions */
            font-size: 0.75rem; /* Smaller font size */
            border-radius: 0.5rem; /* More rounded */
            font-weight: 600;
            transition: background-color 0.2s, color 0.2s, transform 0.2s;
        }
        .action-button.pay-fee {
            background-color: #10b981; /* Tailwind green-500 */
            color: white;
        }
        .action-button.pay-fee:hover {
            background-color: #059669; /* Darker green */
            transform: translateY(-1px);
        }
        .action-button.edit {
            color: #3b82f6; /* Tailwind blue-500 */
        }
        .action-button.edit:hover {
            color: #2563eb; /* Darker blue */
        }
        .action-button.delete {
            color: #ef4444; /* Tailwind red-500 */
        }
        .action-button.delete:hover {
            color: #dc2626; /* Darker red */
        }

        /* Pagination styling adjustments */
        .pagination-container nav {
            display: flex;
            justify-content: center; /* Center pagination links */
            padding-top: 1rem;
        }
        .pagination-container svg.w-5.h-5 { /* Adjust icon size if needed */
            width: 1rem;
            height: 1rem;
        }
        .pagination-container .leading-5 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .pagination-container .text-sm {
            font-size: 0.875rem; /* Standard small font */
        }
        .pagination-container .bg-white {
            background-color: transparent; /* Remove white background */
        }
        .pagination-container .border-gray-300 {
            border-color: #d1d5db; /* Tailwind gray-300 */
        }
        .pagination-container .text-gray-700 {
            color: #4b5563; /* Tailwind gray-700 */
        }
        .pagination-container .hover\:bg-gray-50:hover {
            background-color: #f9fafb; /* Light hover */
        }
        .pagination-container .focus\:border-blue-300:focus {
            border-color: #93c5fd; /* Tailwind blue-300 */
        }
        .pagination-container .focus\:ring-blue-200:focus {
            --tw-ring-color: rgba(191, 219, 254, 0.5); /* Tailwind blue-200 */
        }
        .pagination-container .text-white.bg-blue-600 { /* Active page */
            background-color: #3b82f6; /* Tailwind blue-500 */
            color: #ffffff;
            border-color: #3b82f6;
            font-weight: 600;
        }
    </style>

    
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4"> 
        <a href="<?php echo e(route('students.create')); ?>" class="w-full md:w-auto btn-start text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition duration-300 text-center flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Add New Student</span>
        </a>

        <!-- Filter Form -->
        <form method="GET" action="<?php echo e(route('students.index')); ?>" class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
            <select name="batch_filter" class="block w-full md:w-48 bg-white border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700"> 
                <option value="">All Batches</option>
                <?php $__currentLoopData = $allBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($batch->id); ?>" <?php echo e(request('batch_filter') == $batch->id ? 'selected' : ''); ?>>
                        <?php echo e($batch->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2"> 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                <span>Filter</span>
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200"> 
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="table-header"> 
                        <th class="py-3 px-4 md:px-6 text-left">Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th>
                        <th class="py-3 px-4 md:px-6 text-left">Class</th>
                        <th class="py-3 px-4 md:px-6 text-left">Phone</th>
                        <th class="py-3 px-4 md:px-6 text-left">Fees</th>
                        <th class="py-3 px-4 md:px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-100 table-row-hover" 
                            data-href="<?php echo e(route('students.edit', $student)); ?>">

                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap"><?php echo e($student->name); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->batch->name ?? 'N/A'); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->class); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->phone); ?></td>
                            <td class="py-3 px-4 md:px-6 text-left">₹<?php echo e(number_format($student->fees)); ?></td>

                            <td class="py-3 px-4 md:px-6 text-left whitespace-nowrap">
                                <div class="flex gap-3 items-center">
                                    
                                <a href="<?php echo e(route('students.profile', $student)); ?>" 
                                   onclick="event.stopPropagation()"
                                   title="View Profile"
                                   class="text-purple-600 hover:text-purple-900 font-semibold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                    <a href="<?php echo e(route('fees.index', ['student_id' => $student->id])); ?>"
                                       onclick="event.stopPropagation()"
                                       class="action-button pay-fee"> 
                                        Pay Fee
                                    </a>
                                    <a href="<?php echo e(route('students.edit', $student)); ?>"
                                       onclick="event.stopPropagation()"
                                       class="action-button edit"> 
                                        Edit
                                    </a>
                                    <form action="<?php echo e(route('students.destroy', $student)); ?>" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this student?');"
                                          onclick="event.stopPropagation()">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-button delete"> 
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-6 px-4 text-center text-gray-500 text-base">No students found. Add one to get started!</td> 
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-white pagination-container"> 
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