<?php

use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

// products
Route::get('/catalog', [ProductController::class, 'catalog'])->name('products.catalog');

// orders
Route::post('/create-order', [OrderController::class, 'create'])->name('orders.create');
