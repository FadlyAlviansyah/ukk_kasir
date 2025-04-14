<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('pages.login');
    })->name('login');
    
    Route::post('/login', [AuthController::class,'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class,'index'])->name('dashboard');

    Route::prefix('/product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class,'index'])->name('home');

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/create', [ProductController::class,'create'])->name('create');
            Route::post('/create', [ProductController::class,'store'])->name('store');
            Route::get('/edit/{id}', [ProductController::class,'edit'])->name('edit');
            Route::patch('/{id}', [ProductController::class,'update'])->name('update');
            Route::patch('/update-stock/{id}', [ProductController::class,'updateStock'])->name('updateStock');
            Route::delete('/{id}', [ProductController::class,'destroy'])->name('destroy');
            Route::get('/export-excel', [ProductController::class,'exportExcel'])->name('exportExcel');
        });
    });

    Route::prefix('/transaction')->name('transaction.')->group(function () {
        Route::get('/', [TransactionController::class,'index'])->name('home');
        Route::get('/export-excel', [TransactionController::class,'exportExcel'])->name(name: 'exportExcel');
        
        Route::middleware(['role:cashier'])->group(function() {
            Route::get('/create', [TransactionController::class,'create'])->name('create');
            Route::post('/create', [TransactionController::class,'store'])->name('store');
            Route::get('/create-member', [TransactionController::class,'createMember'])->name('createMember');
            Route::post('/create-member', [TransactionController::class,'storeMember'])->name('storeMember');
            Route::get('/detail-print/{id}', [TransactionController::class,'detailPrint'])->name('detailPrint');
            Route::get('/detail-print/print/{id}', [TransactionController::class,'print'])->name('print');
        });
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('/user')->name('user.')->group(function () {
            Route::get('/', [UserController::class,'index'])->name('home');
            Route::get('/create', action: [UserController::class,'create'])->name('create');
            Route::post('/create', [UserController::class,'store'])->name('store');
            Route::get('/edit/{id}', [UserController::class,'edit'])->name('edit');
            Route::patch('/{id}', [UserController::class,'update'])->name('update');
            Route::delete('/{id}', [UserController::class,'destroy'])->name('destroy');
            Route::get('/export-excel', [UserController::class,'exportExcel'])->name('exportExcel');
        });
    });

    Route::get('/logout', [AuthController::class,'logout'])->name('logout');
});
