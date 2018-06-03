<?php

use Illuminate\Http\Request;

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
//
Route::prefix("/v1")->middleware(['api', 'cors'])->group(function () {
    Route::post('auth/login', 'Api\AuthController@login');
    Route::post('auth/register', 'Api\AuthController@register');
    Route::middleware(['jwt'])->group(function () {
        Route::post('auth/logout', 'Api\AuthController@logout');
    });
});