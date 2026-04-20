<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BeasiswaController;

/*
|--------------------------------------------------------------------------
| SISTEM MASTER SETTING
|--------------------------------------------------------------------------
*/
Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
Route::post('/setting/plant/add', [SettingController::class, 'addPlant']);
Route::post('/setting/plant/update/{id}', [SettingController::class, 'updatePlant']);
Route::get('/setting/plant/delete/{id}', [SettingController::class, 'deletePlant']);
Route::post('/setting/background/update', [SettingController::class, 'updateBackground']);

/*
|--------------------------------------------------------------------------
| SISTEM UNDIAN BEASISWA
|--------------------------------------------------------------------------
*/
Route::get('/beasiswa-admin', [BeasiswaController::class, 'admin'])->name('beasiswa.admin');
Route::post('/beasiswa/peserta/simpan', [BeasiswaController::class, 'simpanPeserta']);
Route::post('/beasiswa/peserta/import', [BeasiswaController::class, 'importPeserta']);
Route::post('/beasiswa/kategori/simpan', [BeasiswaController::class, 'simpanKategori']);
Route::get('/beasiswa/kocok', [BeasiswaController::class, 'kocokBeasiswa']);
Route::post('/beasiswa/kuota-plant/update', [BeasiswaController::class, 'updateKuotaPlant']); // Rute Baru
Route::post('/beasiswa/peserta/update/{id}', [BeasiswaController::class, 'updatePeserta']);
Route::post('/beasiswa/peserta/delete/{id}', [BeasiswaController::class, 'deletePeserta']);
Route::post('/beasiswa/peserta/reset', [BeasiswaController::class, 'resetPeserta']);
Route::get('/beasiswa-undi', [BeasiswaController::class, 'layarUndian'])->name('beasiswa.index');
Route::get('/beasiswa/export-pemenang', [BeasiswaController::class, 'exportPemenang'])->name('beasiswa.export');

/*
|--------------------------------------------------------------------------
| SISTEM UNDIAN DOORPRIZE (EXISTING)
|--------------------------------------------------------------------------
*/
Route::get('/', [EmployeeController::class, 'indexAdmin'])->name('admin.index');
Route::get('/undian', [EmployeeController::class, 'indexGacha'])->name('gacha.index');
Route::post('/admin/add-employee', [EmployeeController::class, 'addEmployee']);
Route::post('/admin/import-employees', [EmployeeController::class, 'importEmployees']);
Route::post('/admin/update/{id}', [EmployeeController::class, 'updateEmployee']);
Route::get('/admin/delete/{id}', [EmployeeController::class, 'deleteEmployee']);
Route::post('/admin/add-prize', [EmployeeController::class, 'addPrize']);
Route::get('/admin/delete-prize/{id}', [EmployeeController::class, 'deletePrize']);
Route::post('/win', [EmployeeController::class, 'storeWinner']);
