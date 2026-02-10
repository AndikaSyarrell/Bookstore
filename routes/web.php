<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\BuyerProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

// Guest routes (accessible only when not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot Password Routes
    Route::get('/forgot-password', [ResetPasswordController::class, 'showForgotForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink'])
        ->name('password.email');

    // Reset Password Routes
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])
        ->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [BuyerController::class, 'index'])->name('homepage');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //Buyer Routes
    Route::prefix('/home')->controller(BuyerController::class)->group(function () {
        Route::get('/profile', 'showProfile')->name('profile.buyer');

        Route::get('/settings', 'showSettings')->name('settings');
        Route::post('/settings/update', 'updateSettings')->name('settings.update');

        Route::get('/book-detail', 'showBookDetails')->name('book.details');

        Route::get('/cart', 'showCart')->name('cart');
        Route::get('/cart/checkout', 'showcheckout')->name('cart.checkout');
        Route::get('/cart/checkout/payment', 'showPayment')->name('cart.checkout.payment');

        Route::get('/chats', 'showChats')->name('chats');

        Route::get('/track', 'showTracks')->name('tracks');
    });

    Route::prefix('/home/cart')->controller(CartController::class)->group(function () {
        Route::get('/get', 'getCart')->name('cart.get');
        Route::post('/store', 'add')->name('cart.store');
        Route::put('/update/{id}', 'update')->name('cart.update');
        Route::delete('/remove/{id}', 'remove')->name('cart.remove');
        Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('checkout.process');
    });

     // Checkout process
    Route::prefix('/home/profile')->controller(BuyerProfileController::class)->group(function(){
        Route::get('/get','show')->name('Bprofile.show');
        Route::put('/update}','update')->name('Bprofile.update');
        Route::post('/update-photo','updatePhoto')->name('Bprofile.update-photo');
        Route::put('/update/password','updatePassword')->name('Bprofile.update-password');
        Route::delete('/remove-photo','deletePhoto')->name('Bprofile.remove-photo');
    });

    Route::prefix('/home/orders')->controller(OrderController::class)->group(function () {
        // List orders
        Route::get('/', 'index')->name('order.index');
        
        // View order detail
        Route::get('/{id}', 'show')->name('order.show');
        
        // Upload payment proof (Buyer)
        Route::post('/{id}/upload-proof', 'uploadPaymentProof')->name('order.upload-proof');
        
        // Verify payment (Seller)
        Route::post('/{id}/verify-payment', 'verifyPayment')->name('order.verify-payment');
        
        // Update order status (Seller)
        Route::post('/{id}/update-status', 'updateStatus')->name('order.update-status');
        
        // Cancel order (Buyer)
        Route::post('/{id}/cancel', 'cancel')->name('order.cancel');
    });

    
    //dashboard admin and seller
    Route::prefix('/dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('/profile', 'showProfile')->name('profile');

        Route::get('/settings', 'showSettings')->name('settings');
        Route::post('/settings/update', 'updateSettings')->name('settings.update');

        Route::get('/users', 'showUsers')->name('users');

        Route::get('/categories', 'showCategories')->name('categories');

        Route::get('/products', 'showProducts')->name('products');

        Route::get('/pre-order', 'showOrders')->name('preorders');
        // View order detail
        Route::get('/pre-order/{id}', [OrderController::class, 'show'])->name('order.show-seller');

        Route::get('/chats', 'showChats')->name('chats');
    });

    Route::prefix('/dashboard/categories')->controller(CategoryController::class)->group(function () {
        Route::post('/store', 'store')->name('categories.store');
        Route::post('/{category}/update', 'update')->name('categories.update');
        Route::delete('/{category}/delete', 'destroy')->name('categories.delete');
        Route::get('/categories/search', 'search')->name('categories.search');
    });

    Route::prefix('/dashboard/products')->controller(ProductsController::class)->group(function () {
        Route::get('/create', 'create')->name('products.create');
        Route::get('{id}/edit', 'edit')->name('products.edit');
        Route::post('/store', 'store')->name('products.store');
        Route::post('/{product}/update', 'update')->name('products.update');
        Route::delete('/{product}/delete', 'destroy')->name('products.delete');
        Route::get('/search', 'search')->name('products.search');
    });

    Route::prefix('/dashboard/profile')->controller(UserController::class)->group(function () {
        Route::post('/update', 'update')->name('profile.update');
        Route::post('/password/change', 'changePassword')->name('profile.password.change');
        Route::post('/delete', 'destroy')->name('profile.delete');
    });

    Route::prefix('/dashboard/users')->controller(UserController::class)->group(function () {
        Route::get('/create', 'create')->name('users.create');
        Route::post('/store', 'store')->name('users.store');
        Route::get('/{id}/edit', 'edit')->name('users.edit');
        Route::post('/{id}/update', 'update')->name('users.update');
        Route::delete('/{id}/delete', 'destroy')->name('users.delete');
    });

    Route::prefix('/dashboard/chats')->controller(MessageController::class)->group(function () {
        Route::get('/receive/{chat}', 'getMessages')->name('messages.show');
        Route::post('/sent/{chat}', 'store')->name('messages.store');
    });
});

// Public route
Route::get('/', function () {
    return view('welcome');
})->name('home');
