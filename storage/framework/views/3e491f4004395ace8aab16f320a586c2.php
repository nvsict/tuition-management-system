

<?php $__env->startSection('title', $student->name . ' - Profile'); ?>
<?php $__env->startSection('header_title', 'Student Profile'); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Profile Card & Quick Actions -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Profile Card -->
            <div class="bg-white rounded-xl shadow-xl p-6 text-center transition duration-300 transform hover:shadow-2xl">
                <!-- Profile Picture Placeholder -->
                <div class="mb-4 w-32 h-32 rounded-full mx-auto overflow-hidden border-4 border-gray-200 shadow-lg bg-gray-100">
    <?php if($student->profile_picture_url): ?>
        <img src="<?php echo e(asset('storage/' . $student->profile_picture_url)); ?>"
             alt="<?php echo e($student->name); ?>"
             class="w-full h-full object-cover">
    <?php else: ?>
        <?php
            $nameParts = explode(' ', $student->name);
            $initials = count($nameParts) >= 2 ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts)-1], 0, 1)) : strtoupper(substr($student->name, 0, 1));
        ?>
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-green-500">
            <span class="text-4xl font-bold text-white"><?php echo e($initials); ?></span>
        </div>
    <?php endif; ?>
</div>


                <h2 class="text-2xl font-bold text-gray-800"><?php echo e($student->name); ?></h2>
                <p class="text-gray-600">Roll No: <?php echo e($student->roll_no); ?></p>
                <p class="text-sm text-gray-500 mt-1">Joined: <?php echo e($student->join_date->format('d M Y')); ?></p>

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center space-x-4">
                    <a href="<?php echo e(route('students.edit', $student)); ?>" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-150">
                         <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit
                    </a>
                    <a href="<?php echo e(route('fees.index', ['student_id' => $student->id])); ?>" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-150">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Fee Ledger
                    </a>
                </div>
            </div>

            <!-- Contact & Batch Details -->
            <div class="bg-white rounded-xl shadow-xl p-6 transition duration-300 transform hover:shadow-2xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Details</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Batch:</dt>
                        <dd class="font-medium text-gray-800"><?php echo e($student->batch->name ?? 'N/A'); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Class:</dt>
                        <dd class="font-medium text-gray-800"><?php echo e($student->class); ?></dd>
                    </div>
                     <div class="flex justify-between">
                        <dt class="text-gray-500">Phone:</dt>
                        <dd class="font-medium text-gray-800 font-mono"><?php echo e($student->phone); ?></dd>
                    </div>
                     <div class="flex justify-between">
                        <dt class="text-gray-500">Monthly Fee:</dt>
                        <dd class="font-medium text-gray-800">₹<?php echo e(number_format($student->fees, 2)); ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Right Column: Attendance & Fees -->
        <div class="lg:col-span-2 space-y-6">

             <!-- Attendance Summary -->
            <div class="bg-white rounded-xl shadow-xl p-6 transition duration-300 transform hover:shadow-2xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Attendance Summary</h3>
                <?php if($attendanceSummary->total_days > 0): ?>
                    <div class="flex justify-around items-center text-center">
                        <div>
                            <p class="text-3xl font-bold text-green-600"><?php echo e($attendanceSummary->present_percentage); ?><span class="text-xl">%</span></p>
                            <p class="text-sm text-gray-500">Present</p>
                        </div>
                         <div>
                            <p class="text-2xl font-semibold text-green-600"><?php echo e($attendanceSummary->total_present); ?></p>
                            <p class="text-xs text-gray-500">Days Present</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold text-red-600"><?php echo e($attendanceSummary->total_absent); ?></p>
                            <p class="text-xs text-gray-500">Days Absent</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold text-gray-600"><?php echo e($attendanceSummary->total_days); ?></p>
                            <p class="text-xs text-gray-500">Total Days Marked</p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                         <a href="<?php echo e(route('attendance.report', ['student_id' => $student->id])); ?>" class="text-sm font-medium text-blue-600 hover:underline">View Full Report &rarr;</a>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500 py-4">No attendance recorded yet.</p>
                <?php endif; ?>
            </div>

            <!-- Fee Status & Recent Transactions -->
            <div class="bg-white rounded-xl shadow-xl p-6 transition duration-300 transform hover:shadow-2xl">
                 <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Fee Status</h3>

                 <!-- Current Balance -->
                 <div class="mb-4 p-4 rounded-lg border-2
                     <?php
                         $status_class = 'bg-gray-50 border-gray-200';
                         if ($currentBalance > 0) { $status_class = 'bg-red-50 border-red-300'; }
                         elseif ($currentBalance < 0) { $status_class = 'bg-blue-50 border-blue-300'; }
                     ?>
                     <?php echo e($status_class); ?>

                 ">
                     <label class="block text-xs font-medium text-gray-500 uppercase">Current Balance</label>
                     <?php $balanceColor = $currentBalance > 0 ? 'text-red-700' : 'text-green-700'; ?>
                     <p class="text-3xl font-bold <?php echo e($balanceColor); ?>">
                         ₹<?php echo e(number_format(abs($currentBalance), 2)); ?>

                         <span class="text-xl font-medium"><?php echo e($currentBalance > 0 ? 'Due' : ($currentBalance < 0 ? 'Credit' : 'Settled')); ?></span>
                     </p>
                 </div>

                 <h4 class="text-md font-semibold text-gray-600 mb-2 mt-4">Recent Transactions</h4>
                 <div class="overflow-x-auto border rounded-lg max-h-60">
                     <table class="min-w-full leading-normal">
                         <thead class="sticky top-0 bg-gray-50 z-10">
                             <tr class="text-gray-500 uppercase text-xs">
                                 <th class="py-2 px-3 text-left">Date</th>
                                 <th class="py-2 px-3 text-left">Description</th>
                                 <th class="py-2 px-3 text-right">Amount (₹)</th>
                             </tr>
                         </thead>
                         <tbody class="text-gray-700 text-sm">
                             <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                 <tr class="border-t border-gray-100 <?php echo e($loop->even ? 'bg-gray-50/50' : ''); ?>">
                                     <td class="py-2 px-3 text-left"><?php echo e($tx->date->format('d-M-y')); ?></td>
                                     <td class="py-2 px-3 text-left"><?php echo e($tx->description); ?></td>
                                     <td class="py-2 px-3 text-right font-semibold <?php echo e($tx->amount > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                                         <?php echo e($tx->amount > 0 ? '+' : ''); ?><?php echo e(number_format($tx->amount, 2)); ?>

                                     </td>
                                 </tr>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                 <tr>
                                     <td colspan="3" class="py-4 px-3 text-center text-gray-400">No transactions recorded yet.</td>
                                 </tr>
                             <?php endif; ?>
                         </tbody>
                     </table>
                 </div>
                 <div class="mt-4 text-right">
                      <a href="<?php echo e(route('fees.index', ['student_id' => $student->id])); ?>" class="text-sm font-medium text-blue-600 hover:underline">View Full Ledger &rarr;</a>
                 </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/students/profile.blade.php ENDPATH**/ ?>