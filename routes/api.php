<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', LoginController::class)->name('login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', LogoutController::class);
    Route::get('/dashboard', DashboardController::class);
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/all', [UserController::class, 'allUser']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/save', [UserController::class, 'store']);
        Route::delete('/delete/{user}', [UserController::class, 'destroy']);
    });
    Route::group(['prefix' => 'withdraw'], function () {
        Route::get('/', [WithdrawController::class, 'index']);
        Route::get('/show/{id}', [WithdrawController::class, 'show']);
        Route::get('/count/{userId}', [
            WithdrawController::class,
            'countWithdraw',
        ]);
        Route::post('/save', [WithdrawController::class, 'store']);
        Route::delete('/delete/{withdraw}', [
            WithdrawController::class,
            'destroy',
        ]);
    });
    Route::group(['prefix' => 'deposit'], function () {
        Route::get('/', [DepositController::class, 'index']);
        Route::get('/show/{id}', [DepositController::class, 'show']);
        Route::get('/count/{userId}', [
            DepositController::class,
            'countDeposit',
        ]);
        Route::post('/save', [DepositController::class, 'store']);
        Route::delete('/delete/{deposit}', [
            DepositController::class,
            'destroy',
        ]);
    });
    Route::group(['prefix' => 'savings'], function () {
        Route::get('/', [SavingsController::class, 'index']);
        Route::get('/show/{savings:user_id}', [
            SavingsController::class,
            'show',
        ]);
        Route::delete('/delete/{savings}', [
            SavingsController::class,
            'destroy',
        ]);
    });
    Route::prefix('profile')->group(function () {
        Route::post('/save', [ProfileController::class, 'store']);
        Route::get('/photo/{id}', [ProfileController::class, 'index']);
    });
});
