<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['as' => 'admin.', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {

    Auth::routes(['register' => false]);

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login-form');
        Route::post('/confirm-login', [AdminController::class, 'login'])->name('login');
    });

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::resource('rooms', RoomController::class);
        Route::resource('booking', BookingController::class);
        Route::patch('booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        Route::resource('user', UserController::class);
        
        // Admin creation routes
        Route::get('/admin/create', [AdminController::class, 'createAdmin'])->name('admin.create');
        Route::post('/admin/store', [AdminController::class, 'storeAdmin'])->name('admin.store');
    });
});

