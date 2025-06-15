<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Rute Publik
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/landing', [LandingController::class, 'index'])->name('landing');
Route::get('/interval', [LandingController::class, 'interval'])->name('interval');
Route::get('/order-form', [LandingController::class, 'form'])->name('order.form');
Route::post('/order-store', [LandingController::class, 'store'])->name('order.store');

// Rute Autentikasi
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// // Rute yang Membutuhkan Autentikasi dan Role Tertentu
Route::middleware(['auth'])->group(function () {
    
    Route::middleware(['role:Administrator,Supplier'])->group(function () {
        Route::get('/dashboard', [SupplierController::class, 'index'])->name('stock');
    });
    
    Route::middleware(['role:Administrator'])->group(function () {
        Route::get('/dashboard/stock/{stock}', [SupplierController::class, 'show'])->name('stock.show');
        Route::post('/dashboard/stock/{stock}/toggle-status', [SupplierController::class, 'toggle'])->name('stock.toggle');
        Route::post('/stocks', [SupplierController::class, 'store'])->name('stock.store');
        Route::put('/dashboard/stock/{stock}', [SupplierController::class, 'update'])->name('stock.update');
        Route::post('/dashboard/stock/{stock}/order', [SupplierController::class, 'orderStock'])->name('stock.order');
        Route::delete('/dashboard/stock/{stock}', [SupplierController::class, 'destroy'])->name('stock.destroy');
        Route::get('/order-list', [AdminController::class, 'order'])->name('order.list');
        Route::get('/orders/{order}/show', [AdminController::class, 'show'])->name('order.show');
        Route::post('/orders/{order}/update-status', [AdminController::class, 'update'])->name('order.update');
        Route::get('/orders/{orderId}/attachment', [AdminController::class, 'serveAttachment'])->name('order.attachments');
        Route::post('/orders/{order}/adjust-stock', [AdminController::class, 'adjustStockForOrder'])->name('order.adjust');
        Route::get('/forecast', [AdminController::class, 'forecast'])->name('forecast');
    });
    
    Route::middleware(['role:Supplier'])->group(function () {
        Route::post('/dashboard/stock-orders/{stockOrder}/fulfill', [SupplierController::class, 'fulfillOrder'])->name('stock.fulfill');
    });

});