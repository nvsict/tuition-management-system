

<?php $__env->startSection('title', 'Fee Reminders'); ?>
<?php $__env->startSection('header_title', 'Students with Due Fees'); ?>

<?php $__env->startSection('content'); ?>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <p class="text-gray-600">This list automatically shows all students with an outstanding fee balance. Click the button to send a reminder via WhatsApp.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 md:px-6 text-left">Student Name</th>
                        <th class="py-3 px-4 md:px-6 text-left">Batch</th>
                        <th class="py-3 px-4 md:px-6 text-right">Amount Due (₹)</th>
                        <th class="py-3 px-4 md:px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $studentsWithDues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-200 hover:bg-red-50">
                            <td class="py-3 px-4 md:px-6 text-left font-semibold">
                                <a href="<?php echo e(route('fees.index', ['student_id' => $student->id])); ?>" class="text-blue-600 hover:underline">
                                    <?php echo e($student->name); ?>

                                </a>
                            </td>
                            <td class="py-3 px-4 md:px-6 text-left"><?php echo e($student->batch->name ?? 'N/A'); ?></td>
                            <td class="py-3 px-4 md:px-6 text-right font-bold text-red-700">
                                ₹<?php echo e(number_format($student->transactions_sum_amount, 2)); ?>

                            </td>
                            <td class="py-3 px-4 md:px-6 text-center">
                                <?php
                                    // Prepare the WhatsApp message
                                    $instituteName = setting('institute_name', 'our tuition class'); // Get institute name from settings
                                    $studentName = $student->name;
                                    $amountDue = number_format($student->transactions_sum_amount, 2);
                                    $phoneNumber = $student->phone; // Assuming 10 digits
                                    $countryCode = '91'; // India's country code

                                    // Construct the polite message for parents
                                    $message = "Dear Parent,\n\n";
                                    $message .= "This is a friendly reminder from {$instituteName} regarding the outstanding fee balance for {$studentName}.\n\n";
                                    $message .= "Amount Due: ₹{$amountDue}\n\n";
                                    $message .= "We kindly request you to clear the dues at your earliest convenience.\n\n";
                                    $message .= "Thank you.";

                                    // URL Encode the message for the link
                                    $encodedMessage = urlencode($message);

                                    // Construct the full WhatsApp URL
                                    $whatsAppUrl = "https://wa.me/{$countryCode}{$phoneNumber}?text={$encodedMessage}";
                                ?>

                                <!-- WhatsApp Reminder Button -->
                                <a href="<?php echo e($whatsAppUrl); ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1 px-3 rounded-full transition duration-300">
                                   <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.003 11.237a.749.749 0 00-.53-.217c-.187 0-.365.074-.5.209l-.89 1.018a.75.75 0 01-1.127.051l-2.028-1.574a.75.75 0 01-.192-1.258l.89-1.018a.75.75 0 00-.05-1.127l-1.574-2.028a.75.75 0 00-1.258-.192l-1.018.89a.75.75 0 00-.209.5l-.218.531c-.134.33-.035.7.275.986l4.67 4.203c.33.296.757.37 1.137.197l.53-.217a.748.748 0 00.5-.209l1.018-.89a.75.75 0 00.051-1.127l-1.574-2.028a.75.75 0 00-1.127-.051l-1.018.89c-.066.075-.16.117-.258.117a.36.36 0 01-.258-.117l-3.34-2.997a.36.36 0 01-.117-.258c0-.1.042-.192.117-.258l.89-1.018a.36.36 0 01.258-.117c.1 0 .192.042.258.117l1.309 1.69a.36.36 0 01.117.258.75.75 0 00.94.577l2.028-1.574a.75.75 0 011.258.192l1.018.89c.134.117.217.28.217.45 0 .17-.083.333-.217.45z"/></svg> <!-- Simple WhatsApp-like icon -->
                                    Remind
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="py-10 px-4 text-center text-gray-500">
                                <div class="text-lg">🎉</div>
                                <p class="mt-2 font-semibold">No students have outstanding dues!</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/transactions/reminders.blade.php ENDPATH**/ ?>