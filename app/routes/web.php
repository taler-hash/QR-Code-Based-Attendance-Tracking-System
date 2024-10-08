<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SectionController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Section Routes
    Route::controller(SectionController::class)
    ->prefix('/sections')
    ->group(function() {
        Route::get('/', 'index')->name('sections');
        Route::get('/get', 'get')->name('sections.get');
        Route::get('/studentsundersectionof', 'studentsUnderSectionOf')->name('sections.studentsundersectionof');
        Route::get('/show', 'show')->name('sections.show');
        Route::put('/create', 'create')->name('sections.create');
        Route::get('/read', 'read')->name('sections.read');
        Route::patch('/update', 'update')->name('sections.update');
        Route::delete('/delete', 'delete')->name('sections.delete');
    });

    // Teacher Routes
    Route::controller(TeacherController::class)
    ->prefix('/teachers')
    ->group(function() {
        Route::get('/', 'index')->name('teachers');
        Route::put('/create', 'create')->name('teachers.create');
        Route::get('/read', 'read')->name('teachers.read');
        Route::patch('/update', 'update')->name('teachers.update');
        Route::delete('/delete', 'delete')->name('teachers.delete');
    });

    // Student Routes
    Route::controller(StudentController::class)
    ->prefix('/students')
    ->group(function() {
        Route::get('/', 'index')->name('students');
        Route::put('/create', 'create')->name('students.create');
        Route::get('/get', 'get')->name('students.get');
        Route::get('/read', 'read')->name('students.read');
        Route::patch('/update', 'update')->name('students.update');
        Route::delete('/delete', 'delete')->name('students.delete');
    });

     // Attendance Routes
    Route::controller(AttendanceController::class)
    ->prefix('/attendance')
    ->group(function() {
        Route::get('/', 'index')->name('attendance');
        Route::put('/create', 'create')->name('attendance.create');
        Route::get('/getstudent', 'getStudent')->name('attendance.getStudent');
        Route::get('/read', 'read')->name('attendance.read');
        Route::patch('/update', 'update')->name('attendance.update');
        Route::delete('/delete', 'delete')->name('attendance.delete');
    });
    
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
