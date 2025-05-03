<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Administrator;
use App\Http\Controllers\Api\Auth;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('Auth')->group(function () {
    Route::post('/register', [Auth::class, 'register'])->name('register');
    Route::post('/login', [Auth::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [Auth::class, 'profile']);
        Route::post('/logout', [Auth::class, 'logout']);
    });
});



Route::middleware('auth:sanctum')->group(function () {
Route::prefix('Administrator')->name('Administrator.')->group(function () {
    Route::get('menu', [Administrator::class, 'indexMenu'])->name('menu');
    Route::post('store-menu', [Administrator::class, 'storeMenu'])->name('store.menu');
    Route::get('show-menu/{id}', [Administrator::class, 'showMenu'])->name('show.menu');
    Route::put('update-menu/{id}', [Administrator::class, 'updateMenu'])->name('update.menu');
    Route::delete('delete-menu/{id}', [Administrator::class, 'destroyMenu'])->name('destroy.menu');
});
});




Route::middleware('auth:sanctum')->group(function () {
Route::prefix('Administrator')->name('Administrator.')->group(function () {
    Route::get('submenu', [Administrator::class, 'indexSubMenu'])->name('submenu');
    Route::post('store-submenu', [Administrator::class, 'storeSubMenu'])->name('store.submenu');
    Route::get('show-submenu/{id}', [Administrator::class, 'showSubMenu'])->name('show.submenu');
    Route::put('update-submenu/{id}', [Administrator::class, 'updateSubMenu'])->name('update.submenu');
    Route::delete('delete-submenu/{id}', [Administrator::class, 'destroySubMenu'])->name('destroy.submenu');
});
});


Route::middleware('auth:sanctum')->group(function () {
Route::prefix('Administrator')->name('Administrator.')->group(function () {
    Route::get('role', [Administrator::class, 'indexRole'])->name('role');
    Route::post('store-role', [Administrator::class, 'storeRole'])->name('store.role');
    Route::get('show-role/{id}', [Administrator::class, 'showRole'])->name('show.role');
    Route::put('update-role/{id}', [Administrator::class, 'updateRole'])->name('update.role');
    Route::delete('delete-role/{id}', [Administrator::class, 'destroyRole'])->name('destroy.role');
});
});


Route::prefix('Administrator')->name('Administrator.')->group(function () {
    Route::get('user', [Administrator::class, 'indexUser'])->name('user');
    // Route::post('store-role', [Administrator::class, 'storeRole'])->name('store.role');
    // Route::get('show-role/{id}', [Administrator::class, 'showRole'])->name('show.role');
    // Route::put('update-role/{id}', [Administrator::class, 'updateRole'])->name('update.role');
    // Route::delete('delete-role/{id}', [Administrator::class, 'destroyRole'])->name('destroy.role');
});