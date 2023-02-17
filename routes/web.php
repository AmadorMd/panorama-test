<?php

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

Route::get('/', [siteController::class, 'index']);
Route::get('/new-collection', [siteController::class, 'createCollection'])->name('create-collection');
Route::post('/new-collection', [siteController::class, 'storeCollection'])->name('store-collection');
Route::post('/delete-collection', [siteController::class, 'DeleteCollectionByID'])->name('delete-collection');
