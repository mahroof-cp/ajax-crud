<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


Route::get('users/list', [UserController::class, 'index'])->name('users.index');
Route::get('users-data', [UserController::class, 'getUsers'])->name('users.data');
Route::get('users/{id?}', [UserController::class, 'form'])->name('users.form');
Route::post('users', [UserController::class, 'storeOrUpdate'])->name('users.storeOrUpdate');
Route::delete('users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');

Route::post('users/import', [UserController::class, 'import'])->name('users.import');
Route::get('user/export', [UserController::class, 'export'])->name('users.export');
