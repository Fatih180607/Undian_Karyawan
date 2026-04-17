<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat di mana Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang berisi middleware "web".
|
*/

// --- RUTE HALAMAN UTAMA (ADMIN) ---
// Menampilkan dashboard admin untuk kelola peserta dan hadiah
Route::get('/', [EmployeeController::class, 'indexAdmin'])->name('admin.index');


// --- RUTE HALAMAN UNDIAN (GACHA) ---
// Menampilkan halaman undian visual
Route::get('/undian', [EmployeeController::class, 'indexGacha'])->name('gacha.index');


// --- RUTE KELOLA PESERTA (CRUD) ---
// Menambah peserta baru (ID Karyawan & Nama)
Route::post('/admin/add-employee', [EmployeeController::class, 'addEmployee']);

// Mengupdate data peserta yang sudah ada via Modal
Route::post('/admin/update/{id}', [EmployeeController::class, 'updateEmployee']);

// Menghapus data peserta
Route::get('/admin/delete/{id}', [EmployeeController::class, 'deleteEmployee']);


// --- RUTE KELOLA HADIAH ---
// Menambah hadiah baru beserta upload foto
Route::post('/admin/add-prize', [EmployeeController::class, 'addPrize']);

// Menghapus data hadiah
Route::get('/admin/delete-prize/{id}', [EmployeeController::class, 'deletePrize']);


// --- RUTE API FORMALITAS ---
// Diperlukan agar fetch JavaScript di halaman gacha tidak error 404
// Meskipun kolom is_winner dihapus, route ini tetap membalas "success" ke browser
Route::post('/win', [EmployeeController::class, 'storeWinner']);