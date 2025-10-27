

<?php $__env->startSection('title', $student->exists ? 'Edit Student' : 'Add Student'); ?>
<?php $__env->startSection('header_title', $student->exists ? 'Edit Student' : 'Add New Student'); ?>

<?php $__env->startSection('content'); ?>

<style>
    /* Adjust validation icon alignment */
    .relative > span.text-lg {
        top: 67% !important;
        transform: translateY(-50%) !important;
        right: 0.75rem; /* equivalent to right-3 */
    }

    /* Ensure icon doesn’t overlap text when user types */
    .relative input,
    .relative select {
        padding-right: 2.25rem !important; /* leaves space for icon */
    }

    /* Optional: smooth transition for color change */
    .relative > span.text-lg i {
        transition: color 0.2s ease;
    }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    
    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-8 shadow-sm animate-fade-in" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <strong class="font-semibold text-lg">Whoops! There were some problems:</strong>
            </div>
            <ul class="list-disc ml-8 mt-3 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-extrabold text-gray-900"><?php echo e($student->exists ? 'Edit Student Details' : 'Add New Student'); ?></h2>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo e($student->exists ? 'Update the student information below.' : 'Fill in the details to add a new student.'); ?>

            </p>
        </div>

        <form action="<?php echo e($student->exists ? route('students.update', $student) : route('students.store')); ?>" method="POST" enctype="multipart/form-data" id="studentForm" class="p-6 md:p-8" x-data="studentForm()">
            <?php echo csrf_field(); ?>
            <?php if($student->exists): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                
                
                <div class="md:col-span-2 p-4 bg-gray-50 rounded-xl transition">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>

                    <div class="flex flex-col md:flex-row items-center gap-6">
                        
                        <div class="flex flex-col items-center relative">
                            
                            <div id="captureContainer"
                                 class="w-32 h-32 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-white shadow-sm">
                                <video id="camera" autoplay playsinline class="hidden w-full h-full object-cover"></video>
                                <canvas id="snapshot" class="hidden w-full h-full object-cover"></canvas>
                                <span id="placeholderText" class="text-xs text-gray-400 text-center">Live Preview</span>
                            </div>

                            
                            <div class="flex gap-2 mt-3">
                                <button type="button" id="startCamera"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Start Camera
                                </button>
                                <button type="button" id="takePhoto"
                                        class="hidden bg-orange-500 hover:bg-orange-600 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Capture
                                </button>
                                <button type="button" id="retakePhoto"
                                        class="hidden bg-gray-500 hover:bg-gray-600 text-white text-xs py-2 px-3 rounded-full shadow transition">
                                    Retake
                                </button>
                            </div>
                        </div>

                        
                        <div class="text-gray-400 text-sm">OR</div>

                        
                        <div class="flex flex-col items-center">
                            <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*"
                                   class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                                          file:rounded-full file:border-0 file:text-sm file:font-semibold
                                          file:bg-green-50 file:text-green-700 hover:file:bg-green-100
                                          focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <p class="text-xs text-gray-500 mt-1">Upload JPG/PNG or capture live photo</p>
                        </div>
                    </div>

                    
                    <div id="previewContainer" class="mt-3 text-center">
                        <?php if($student->exists && $student->profile_picture_url): ?>
                            <div>
                                <span class="text-xs text-gray-600">Current Picture:</span>
                                <img src="<?php echo e(asset('storage/' . $student->profile_picture_url)); ?>"
                                     alt="Current Profile Picture"
                                     class="mt-1 w-20 h-20 rounded-full border object-cover mx-auto">
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
                </div>

                
                <div class="md:col-span-2 relative" x-data="{ inputValue: '<?php echo e(old('name', $student->name)); ?>' }">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out <?php echo e($errors->has('name') ? 'border-red-500' : ''); ?>"
                           placeholder="e.g., John Doe"
                           value="<?php echo e(old('name', $student->name)); ?>"
                           maxlength="255"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'text', 3, 255)">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue.length > 0 ? (inputValue.length >= 3 ? 'text-green-500' : 'text-red-500') : ''">
                        <i class="fa-solid" :class="inputValue.length > 0 ? (inputValue.length >= 3 ? 'fa-circle-check' : 'fa-circle-xmark') : ''"></i>
                    </span>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="relative">
                    <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <select name="batch_id" id="batch_id"
                            class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out appearance-none pr-10 <?php echo e($errors->has('batch_id') ? 'border-red-500' : ''); ?>">
                        <option value="">Select a Batch (Optional)</option>
                        <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($batch->id); ?>" <?php echo e(old('batch_id', $student->batch_id) == $batch->id ? 'selected' : ''); ?>>
                                <?php echo e($batch->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="relative" x-data="{ inputValue: '<?php echo e(old('class', $student->class)); ?>' }">
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select name="class" id="class"
                            class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out appearance-none pr-10 <?php echo e($errors->has('class') ? 'border-red-500' : ''); ?>"
                            x-model="inputValue"
                            required
                            @change="validateInput($event.target, 'select')">
                        <option value="">Select Class</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class); ?>" <?php echo e(old('class', $student->class) == $class ? 'selected' : ''); ?>>Class <?php echo e($class); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue ? 'text-green-500' : (inputValue.length > 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue ? 'fa-circle-check' : (inputValue.length > 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    <?php $__errorArgs = ['class'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="relative" x-data="{ inputValue: '<?php echo e(old('phone', $student->phone)); ?>' }">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile No (10 digits) <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="phone"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out <?php echo e($errors->has('phone') ? 'border-red-500' : ''); ?>"
                           placeholder="e.g., 9876543210"
                           value="<?php echo e(old('phone', $student->phone)); ?>"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'phone')">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue.length === 10 ? 'text-green-500' : (inputValue.length > 0 && inputValue.length !== 10 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue.length === 10 ? 'fa-circle-check' : (inputValue.length > 0 && inputValue.length !== 10 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="relative" x-data="{ inputValue: '<?php echo e(old('fees', $student->fees)); ?>' }">
                    <label for="fees" class="block text-sm font-medium text-gray-700 mb-1">Fees per month (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="fees" id="fees"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out <?php echo e($errors->has('fees') ? 'border-red-500' : ''); ?>"
                           placeholder="e.g., 1500"
                           value="<?php echo e(old('fees', $student->fees)); ?>"
                           min="0"
                           step="0.01"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'number', 0)">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue > 0 ? 'text-green-500' : (inputValue.length > 0 && inputValue <= 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue > 0 ? 'fa-circle-check' : (inputValue.length > 0 && inputValue <= 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    <?php $__errorArgs = ['fees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="md:col-span-2 relative" x-data="{ inputValue: '<?php echo e(old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d'))); ?>' }">
                    <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date <span class="text-red-500">*</span></label>
                    <input type="date" name="join_date" id="join_date"
                           class="block w-full px-4 py-2 text-base bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 ease-in-out <?php echo e($errors->has('join_date') ? 'border-red-500' : ''); ?>"
                           value="<?php echo e(old('join_date', $student->join_date ? $student->join_date->format('Y-m-d') : date('Y-m-d'))); ?>"
                           max="<?php echo e(date('Y-m-d')); ?>"
                           x-model="inputValue"
                           required
                           @input="validateInput($event.target, 'date')">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          :class="inputValue ? 'text-green-500' : (inputValue.length > 0 ? 'text-red-500' : '')">
                        <i class="fa-solid" :class="inputValue ? 'fa-circle-check' : (inputValue.length > 0 ? 'fa-circle-xmark' : '')"></i>
                    </span>
                    <?php $__errorArgs = ['join_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <?php if($student->exists): ?>
                    <div class="md:col-span-2 relative">
                        <label for="roll_no" class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                        <input type="text" name="roll_no" id="roll_no"
                               value="<?php echo e(old('roll_no', $student->roll_no)); ?>"
                               class="block w-full px-4 py-2 text-base bg-gray-100 text-gray-600 cursor-not-allowed border border-gray-300 rounded-md shadow-sm"
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">Roll No is auto-generated and cannot be changed.</p>
                    </div>
                <?php endif; ?>

            </div>


            </div>

            
            <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end gap-4">
                <a href="<?php echo e(route('students.index')); ?>" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                    <?php if($student->exists): ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Update Student</span>
                    <?php else: ?>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Save Student</span>
                    <?php endif; ?>
                </button>
            </div>

        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('studentForm', () => ({
        init() {
            this.$nextTick(() => {
                this.$el.querySelectorAll('input, select').forEach(input => {
                    let type = '';
                    if (input.id === 'name') type = 'text';
                    else if (input.id === 'phone') type = 'phone';
                    else if (input.id === 'fees') type = 'number';
                    else if (input.id === 'join_date') type = 'date';
                    else if (input.id === 'class') type = 'select';

                    if (type && input.value) {
                        const min = input.hasAttribute('min') ? parseFloat(input.getAttribute('min')) : 0;
                        const max = input.hasAttribute('maxlength') ? parseFloat(input.getAttribute('maxlength')) : 255;
                        this.validateInput(input, type, min, max);
                    }
                });
            });
        },
        validateInput(inputElement, type, min = null, max = null) {
            const value = inputElement.value;
            let isValid = false;

            if (type === 'text') isValid = value.length >= min && value.length <= max;
            else if (type === 'phone') isValid = /^[0-9]{10}$/.test(value);
            else if (type === 'number') isValid = parseFloat(value) >= min;
            else if (type === 'date') isValid = value !== '';
            else if (type === 'select') isValid = value !== '';

            const parentGroup = inputElement.closest('.relative');
            if (parentGroup) {
                if (isValid) {
                    parentGroup.classList.add('is-valid');
                    parentGroup.classList.remove('is-invalid');
                } else {
                    parentGroup.classList.add('is-invalid');
                    parentGroup.classList.remove('is-valid');
                }
            }
        }
    }));
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const startBtn = document.getElementById("startCamera");
    const takeBtn = document.getElementById("takePhoto");
    const retakeBtn = document.getElementById("retakePhoto");
    const video = document.getElementById("camera");
    const canvas = document.getElementById("snapshot");
    const placeholder = document.getElementById("placeholderText");
    const base64Input = document.getElementById("profile_picture_base64");
    let stream = null;

    startBtn.addEventListener("click", async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.classList.remove("hidden");
            placeholder.classList.add("hidden");
            takeBtn.classList.remove("hidden");
            startBtn.classList.add("hidden");
        } catch (err) {
            alert("Camera access denied or not available.");
        }
    });

    takeBtn.addEventListener("click", () => {
        const ctx = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.classList.remove("hidden");
        video.classList.add("hidden");
        takeBtn.classList.add("hidden");
        retakeBtn.classList.remove("hidden");

        const imageData = canvas.toDataURL("image/png");
        base64Input.value = imageData;
    });

    retakeBtn.addEventListener("click", () => {
        canvas.classList.add("hidden");
        video.classList.remove("hidden");
        retakeBtn.classList.add("hidden");
        takeBtn.classList.remove("hidden");
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Mohit\Desktop\WebDev\Laravel\Tuition Management Software\tuition-management-system\resources\views/students/form.blade.php ENDPATH**/ ?>