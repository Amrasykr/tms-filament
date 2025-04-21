<?php

use App\Exports\AttendanceExport;
use App\Exports\StudentsExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;
use Maatwebsite\Excel\Facades\Excel;


Route::get('/', function () {
    return view('welcome');
})->name('home');;



Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login']);
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');


Route::get('/export/teachers', function () {
    return Excel::download(new StudentsExport, 'teachers.xlsx');
})->middleware('auth:admin');


// Route::get('/export/attendances/{schedule}', function ($scheduleId) {
//     return Excel::download(new AttendanceExport($scheduleId), 'absensi-jadwal-' . $scheduleId . '.xlsx');
// });


