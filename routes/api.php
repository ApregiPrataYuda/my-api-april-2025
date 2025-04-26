<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Administrator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('Administrator')->name('Administrator.')->group(function () {
    Route::get('menu', [Administrator::class, 'indexMenu'])->name('menu');
    Route::post('store-menu', [Administrator::class, 'storeMenu'])->name('store.menu');
    Route::get('show-menu/{id}', [Administrator::class, 'showMenu'])->name('show.menu');
    Route::put('update-menu/{id}', [Administrator::class, 'updateMenu'])->name('update.menu');
    Route::delete('delete-menu/{id}', [Administrator::class, 'destroyMenu'])->name('destroy.menu');
});