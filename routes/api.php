<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
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

Route::group(['prefix' =>'member', 'namespace' => 'App\Http\Controllers'], function(){
    Route::get('{id}', 'MemberController@getById');
    Route::get('/isAccountExit/{account}', 'MemberController@isAccountExit');
    Route::get('/', 'MemberController@get');
    Route::post('/register', 'MemberController@register');
});

