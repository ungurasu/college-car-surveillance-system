<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\AppController;
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

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::group(['middleware' => 'guest'], function() {
    Route::get('/', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'attemptLogin']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', [AppController::class, 'dashboard']);
    Route::post('/filter', [AppController::class, 'filter']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/accounts', [AppController::class, 'accountsPage']);
    Route::get('/newaccount', [AppController::class, 'newAccountForm']);
    Route::get('/edituser/{user_id}', [AppController::class, 'editAccountForm']);
    Route::post('/submitnewaccount', [AppController::class, 'submitNewAccount']);
    Route::post('/submitaccountedit', [AppController::class, 'submitAccountEdit']);
    Route::post('/deleteaccount', [AppController::class, 'deleteAccount']);
    Route::get('/videos/{video}', [AppController::class, 'showVideo']);
    Route::get('/event/{event_id}', [AppController::class, 'showEvent']);
    Route::get('/deleteevent/{event_id}', [AppController::class, 'deleteEvent']);
});
