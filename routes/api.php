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
    Route::get('/user/{id}', 'UserController@find')->name('api.user_find');
    Route::post('/user', 'UserController@create')->name('api.user_create');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::put('/user/{id}/update', 'UserController@update')->name('api.user_update');
        Route::delete('/user/{id}/delete', 'UserController@delete')->name('api.user_delete');
        Route::post('/user/{id}/book/checkin', 'UserController@checkInBooks')->name('api.user_book_checkin');
        Route::post('/user/{id}/book/checkout', 'UserController@checkOutBooks')->name('api.user_book_checkout');
    });

    Route::post('auth/login', 'AccessTokenController@login')->name('api.user_login');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('auth/logout', 'AccessTokenController@logout')->name('api.user_logout');
    });

    Route::get('/book', 'BookController@getAll')->name('api.book_all');
    Route::get('/book/{id}', 'BookController@find')->name('api.book_find');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/book', 'BookController@create')->name('api.book_create');
        Route::put('/book/{id}/update', 'BookController@update')->name('api.book_update');
        Route::delete('/book/{id}/delete', 'BookController@delete')->name('api.book_delete');
    });
});