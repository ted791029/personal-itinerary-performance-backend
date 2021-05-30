<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

Route::group(['prefix' =>'Auth', 'namespace' => 'App\Http\Controllers'], function(){
    Route::get('/isAccountExit/{account}', 'AuthController@isAccountExit');
    Route::post('/register', 'AuthController@register');
    Route::post('/sendVerificationCode', 'AuthController@sendVerificationCode');
});

Route::group(['prefix' =>'Member', 'namespace' => 'App\Http\Controllers'], function(){
    Route::get('{memberToken}', 'MemberController@getByToken');
});

