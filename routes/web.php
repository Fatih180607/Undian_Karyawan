<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

// Halaman Utama Admin
Route::get('/', [EmployeeController::class, 'indexAdmin'])->name('admin.index');

// Halaman Undian/Gacha
Route::get('/undian', [EmployeeController::class, 'indexGacha'])->name('gacha.index');

// Rute CRUD Karyawan
Route::post('/admin/add-employee', [EmployeeController::class, 'addEmployee']);
Route::post('/admin/import-employees', [EmployeeController::class, 'importEmployees']); // <--- Rute Import
Route::post('/admin/update/{id}', [EmployeeController::class, 'updateEmployee']);
Route::get('/admin/delete/{id}', [EmployeeController::class, 'deleteEmployee']);

// Rute CRUD Hadiah
Route::post('/admin/add-prize', [EmployeeController::class, 'addPrize']);
Route::get('/admin/delete-prize/{id}', [EmployeeController::class, 'deletePrize']);

// API untuk mencatat pemenang
Route::post('/win', [EmployeeController::class, 'storeWinner']);