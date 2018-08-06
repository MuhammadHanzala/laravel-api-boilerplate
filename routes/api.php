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
Route::post('verify-2fa/{code}', 'API\UserController@verifyTwoFaCode');
Route::post('user/forgot-password-request', 'API\UserController@ForgotPasswordRequest');
Route::post('social-login', 'API\SocialLoginController@socialLogin');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('user/reset-password', 'API\UserController@resetPasswordByAuth');
    Route::post('user/logout', 'API\UserController@logout');
    Route::put('user/{id}', 'API\UserController@edit');
    Route::post('user/image', 'API\ImageController@addUserImage');
    Route::post('user/logout-other-devices', 'API\UserController@revokeAllTokens');
    Route::post('user/toggle-2fa', 'API\UserController@toggle2fa');

});
