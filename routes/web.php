<?php

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
    return view('welcome');
});

Route::get('/user/verify/{token}', 'API\UserController@verifyUser');
Route::get('/user/password-reset/{token}', 'API\UserController@verifyForgotPasswordToken');
Route::post('/user/reset-password', ['as' => 'updatePassword', 'uses' => 'API\UserController@resetPassword']);

Route::get('/message', function () {
    return view('verifyEmailMessage');
});
