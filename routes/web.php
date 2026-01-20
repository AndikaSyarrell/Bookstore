<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes (accessible only when not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'buyerPage'])->name('main');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::prefix('/dashboard')->controller(DashboardController::class)->group(function(){
        Route::get('/profile', 'showProfile')->name('profile');

        Route::get('/settings', 'showSettings')->name('settings');
        Route::post('/settings/update', 'updateSettings')->name('settings.update');

        Route::get('/users', 'showUsers')->name('users');

        Route::get('/categories', 'showCategories')->name('categories');

        Route::get('/products', 'showProducts')->name('products');
    });

    Route::prefix('/dashboard/categories')->controller(CategoryController::class)->group(function(){
        Route::post('/store', 'store')->name('categories.store');
        Route::post('/{category}/update', 'update')->name('categories.update');
        Route::delete('/{category}/delete', 'destroy')->name('categories.delete');
        Route::get('/categories/search', 'search')->name('categories.search');
    });

    Route::prefix('/dashboard/products')->controller(ProductsController::class)->group(function(){
        Route::get('/create', 'create')->name('products.create');
        Route::post('/store', 'store')->name('products.store');
        Route::post('/{product}/update', 'update')->name('products.update');
        Route::delete('/{product}/delete', 'destroy')->name('products.delete');
        Route::get('products/search', 'search')->name('products.search');
    });

    Route::prefix('/dashboard/profile')->controller(UserController::class)->group(function(){
        Route::post('/update', 'update')->name('profile.update');
        Route::post('/password/change', 'changePassword')->name('profile.password.change');
        Route::post('/delete', 'destroy')->name('profile.delete');
    });

    Route::prefix('/dashboard/users')->controller(UserController::class)->group(function(){
        Route::get('/create', 'create')->name('users.create');
        Route::post('/store', 'store')->name('users.store');
        Route::get('/{id}/edit', 'edit')->name('users.edit');
        Route::post('/{id}/update', 'update')->name('users.update');
        Route::delete('/{id}/delete', 'destroy')->name('users.delete');
    });
});

// Public route
Route::get('/', function () {
    return view('welcome');
})->name('home');
