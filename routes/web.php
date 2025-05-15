<?php

use App\Exports\AttendanceExport;
use App\Exports\StudentsExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ExportController;


Route::get('/', function () {
    return view('welcome');
})->name('home');;



Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login']);
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');


Route::get('/export/teachers', function () {
    return Excel::download(new StudentsExport, 'teachers.xlsx');
})->middleware('auth:admin');

Route::get('/schedules/{schedule}/export', [ExportController::class, 'exportAttendance'])->name('schedules.export');
Route::get('/schedules/{schedule}/export-grades', [ExportController::class, 'exportGrades'])->name('schedules.export-grades');
Route::get('/students/{student}/export-grades/{academic_year}', [ExportController::class, 'exportGradesPerStudent'])->name('students.export-grades');
