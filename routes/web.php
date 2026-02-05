<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock.in');
Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock.out');

// Admin routes
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/admin/calendar', [DashboardController::class, 'calendar'])->name('admin.calendar');
Route::resource('admin/employees', EmployeeController::class, ['as' => 'admin']);

// Attendance routes (merged into EmployeeController)
Route::get('admin/attendances', [EmployeeController::class, 'attendanceIndex'])->name('admin.attendances.index');
Route::get('admin/attendances/{attendance}/edit', [EmployeeController::class, 'attendanceEdit'])->name('admin.attendances.edit');
Route::put('admin/attendances/{attendance}', [EmployeeController::class, 'attendanceUpdate'])->name('admin.attendances.update');
Route::delete('admin/attendances/{attendance}', [EmployeeController::class, 'attendanceDestroy'])->name('admin.attendances.destroy');

// Schedule routes (merged into EmployeeController)
Route::get('admin/schedules', [EmployeeController::class, 'scheduleIndex'])->name('admin.schedules.index');
Route::put('admin/schedules/bulk-update', [EmployeeController::class, 'scheduleBulkUpdate'])->name('admin.schedules.bulk-update');
Route::get('admin/schedules/{user}/edit', [EmployeeController::class, 'scheduleEdit'])->name('admin.schedules.edit');
Route::put('admin/schedules/{user}', [EmployeeController::class, 'scheduleUpdate'])->name('admin.schedules.update');

// User routes
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
Route::put('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
