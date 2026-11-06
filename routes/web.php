<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategorySubcategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (public, slug based SEO-friendly URLs)
|--------------------------------------------------------------------------
*/
Route::controller(ShopController::class)->name('shop.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/category/{slug}', 'category')->name('category');
    Route::get('/subcategory/{slug}', 'subcategory')->name('subcategory');
    Route::get('/products/{slug}', 'product')->name('product');
});

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin panel (CRUD) – requires a logged-in user
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('categories/{category:id}/subcategories', CategorySubcategoryController::class)
        ->name('categories.subcategories');

    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubcategoryController::class);
    Route::resource('products', ProductController::class);
});
