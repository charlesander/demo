<?php

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



    Route::get('auth/test', 'AuthController@test');
    // People
    Route::post('xxxxxxxxxxxc', 'CustomerController@create');
    Route::put('xxxxxxxxxxxc/{customerId}', 'CustomerController@update');
    Route::get('xxxxxxxxxxxc', 'CustomerController@index');
    Route::get('xxxxxxxxxxxc/{customerId}', 'CustomerController@one');

    // Ambassador Shops
    Route::get('xxxxxxxxxxxas', 'AmbassadorShopController@index');
    Route::get('xxxxxxxxxxxas/{ambassadorShopId}', 'AmbassadorShopController@one');
    Route::post('xxxxxxxxxxxas', 'AmbassadorShopController@create');
    Route::post('xxxxxxxxxxxas/{ambassadorShopId}', 'AmbassadorShopController@update');
    Route::delete('xxxxxxxxxxxas/{ambassadorShopId}', 'AmbassadorShopController@delete');

    // Ambassadors
    Route::get('xxxxxxxxxxxaa', 'AmbassadorController@index');
    Route::get('xxxxxxxxxxxaa/{ambassador}', 'AmbassadorController@one');
    Route::get('xxxxxxxxxxxaa/{ambassador}/exigo', 'AmbassadorController@exigo');
    Route::post('xxxxxxxxxxxaa', 'AmbassadorController@create');
    Route::put('xxxxxxxxxxxaa/{ambassador}', 'AmbassadorController@update');
    Route::post('xxxxxxxxxxxaa/{ambassadorId}/upload-photo', 'AmbassadorController@uploadPhoto');
    Route::delete('xxxxxxxxxxxaa/{ambassadorId}', 'AmbassadorController@delete');

 