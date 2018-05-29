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

Route::post('register', 'API\UserController@register');
Route::post('login', 'API\UserController@login');
Route::post('user/forgot-password-request', 'API\UserController@ForgotPasswordRequest');

Route::group(['middleware' => 'auth:api'], function(){
  Route::post('user/reset-password', 'API\UserController@resetPasswordByAuth');
});
