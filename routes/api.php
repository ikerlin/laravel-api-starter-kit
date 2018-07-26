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

/*---------------------------------v1---------------------------------*/
$parameters = ['prefix' => 'v1', 'namespace' => 'Api\v1'];

Route::group($parameters, function () {
    Route::post('/login', 'AuthenticateController@login');
    Route::post('/logout', 'AuthenticateController@logout');
});

Route::group(array_merge($parameters, ['middleware' => 'auth:api']), function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});