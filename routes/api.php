<?php

use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
