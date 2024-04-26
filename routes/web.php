<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    UserController,
    KriteriaController,
    BobotKriteriaController,
    SubKriteriaController,
    BobotSubkriteriaController,
    RekomendasiController,
    AlternatifController,
    ProfilController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['auth']], function(){
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/profil', [App\Http\Controllers\ProfilController::class, 'index'])->name('profil');
    Route::post('/ubah-profil', [App\Http\Controllers\ProfilController::class, 'ubahProfil'])->name('ubah-profil');
});

Route::group(['middleware' => ['auth']], function(){
    Route::resource('/mahasiswa', UserController::class);
    Route::get('datatable-mahasiswa', [UserController::class, 'datatable'])->name('mahasiswa.datatable');
    
    Route::resource('/kriteria', KriteriaController::class);
    Route::get('datatable-kriteria', [KriteriaController::class, 'datatable'])->name('kriteria.datatable');
    
    Route::resource('/bobot-kriteria', BobotKriteriaController::class);
    
    Route::resource('/sub-kriteria', SubKriteriaController::class);
    Route::get('datatable-sub-kriteria', [SubKriteriaController::class, 'datatable'])->name('sub-kriteria.datatable');
    
    Route::get('bobot-sub-kriteria', [BobotSubkriteriaController::class, 'index'])->name('bobot-sub-kriteria.index');
    Route::get('bobot-sub-kriteria/{id}', [BobotSubkriteriaController::class, 'edit'])->name('bobot-sub-kriteria.edit');
    Route::post('bobot-sub-kriteria', [BobotSubkriteriaController::class, 'store'])->name('bobot-sub-kriteria.store');

    Route::resource('/alternatif', AlternatifController::class);
});

Route::group(['middleware' => ['auth']], function(){
    Route::get('rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi.index');
    Route::get('rekomendasi/cari', [RekomendasiController::class, 'create'])->name('rekomendasi.create');
    Route::post('rekomendasi/cari', [RekomendasiController::class, 'cari'])->name('rekomendasi.store');
    Route::post('rekomendasi/kriteria/tambah', [RekomendasiController::class, 'storeKriteria'])->name('rekomendasi.storeKriteria');
    Route::post('rekomendasi/subkriteria/tambah', [RekomendasiController::class, 'storeSubkriteria'])->name('rekomendasi.storeSubkriteria');

    Route::get('/cetak', [RekomendasiController::class, 'cetak'])->name('cetak');
});


Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//verifikasi email user
// Auth::routes(['verify' => true]);


// $this->middleware(['auth','verified']);