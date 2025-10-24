<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TransactionController; // Renamed from FeeController
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BatchController;

// Dashboard Route (Corrected to point to the 'index' method)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Student Routes
Route::resource('students', StudentController::class);

// Batch Routes
Route::resource('batches', BatchController::class);

// Attendance Routes
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
Route::get('/attendance/export', [AttendanceController::class, 'exportCsv'])->name('attendance.export');

// Fee Ledger Routes (Using TransactionController but keeping /fees URL)
Route::get('/fees', [TransactionController::class, 'index'])->name('fees.index');
Route::post('/fees', [TransactionController::class, 'store'])->name('fees.store');
Route::delete('/fees/{transaction}', [TransactionController::class, 'destroy'])->name('fees.destroy');

// Settings Routes
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

// Add this line for the reminder list page
Route::get('/fees/reminders', [TransactionController::class, 'reminders'])->name('fees.reminders');