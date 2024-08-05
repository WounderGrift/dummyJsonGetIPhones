<?php

use Illuminate\Support\Facades\Route;
use Modules\MainPage\App\Http\Controllers\MainController;
use Modules\ProfilePage\App\Http\Controllers\ChartsController;
use Modules\ProfilePage\App\Http\Controllers\EditProfileController;
use Modules\ProfilePage\App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['namespace' => 'App\Http\Controllers'], function() {
    Route::get('/', [MainController::class, 'index'])->name('main.index');
});

Route::middleware(['ownerOrAdmin'])->group(function () {
    Route::prefix('chart')->group(function () {
        Route::get('/profiles', [ChartsController::class, 'profilesTable'])->name('profiles.chart.table');
        Route::post('/profiles/range', [ChartsController::class, 'profilesChart'])
            ->name('profiles.chart.range');
    });

    Route::post('/banned', [ProfileController::class, 'banned'])->prefix('profile')
        ->name('profile.banned');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/logout', [ProfileController::class, 'logout'])->name('profile.logout');
        Route::get('/edit/{cid}', [EditProfileController::class, 'index'])->name('profile.edit');
        Route::put('/update', [EditProfileController::class, 'update'])->name('profile.update');
        Route::get('/{cid}', [ProfileController::class, 'index'])->name('profile.index.cid');
        Route::post('/chart', [ProfileController::class, 'profileChart'])->name('profile.chart');
        Route::post('/create', [ProfileController::class, 'create'])->name('profile.create');
        Route::post('/login', [ProfileController::class, 'login'])->name('profile.login');
    });
});
