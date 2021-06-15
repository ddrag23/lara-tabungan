<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::post('login',LoginController::class)->name('login');
Route::group(['middleware' =>['auth:sanctum']],function() {
    Route::group(['prefix' => 'user'],function(){
        Route::get('/',[UserController::class,'index']);
        Route::get('/{id}',[UserController::class,'show']);
        Route::post('/save',[UserController::class,'store']);
        Route::delete('/delete/{user}',[UserController::class,'destroy']);
    });
    Route::get('/logout',LogoutController::class);
});
