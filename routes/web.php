<?php

use App\Http\Controllers\productController;
use App\Http\Controllers\siteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


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

Route::get('/', [siteController::class, 'index'])->name('home');
Route::get('/new-collection', [siteController::class, 'createCollection'])->name('create-collection');
Route::get('/collection-details/{handle}', [siteController::class, 'showCollection'])->name('show-collection');
Route::post('/new-collection', [siteController::class, 'storeCollection'])->name('store-collection');
Route::post('/delete-collection', [siteController::class, 'DeleteCollectionByID'])->name('delete-collection');

//Products
Route::get('/{collectionHandle}/new-product', [productController::class, 'createView'])->name('create-product');
Route::post('/products/new-product', [productController::class, 'createProduct'])->name('store-product');