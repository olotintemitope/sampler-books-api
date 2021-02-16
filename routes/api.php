<?php

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

Route::group([
    'namespace' => 'Api',
], function () {
    Route::get('/user', 'UserController@getAll')->name('api.user_all');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/user', 'UserController@create')->name('api.user_create');
        Route::put('/user/{id}/update', 'UserController@update')->name('api.user_update');
        Route::delete('/user/{id}/delete', 'UserController@delete')->name('api.user_delete');
    });

    Route::group([], function () {
        Route::post('auth/login', 'AccessTokenController@login')->name('api.user_login');
    });
});