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

Route::redirect('/','products')->name('/');

Auth::routes(['verify'=>true]);


Route::group(['middleware' => ['auth','verified']], function () {
    Route::resource('user_addresses', 'UserAddressController');
});

//商品展示
Route::get('products','ProductsController@index')->name('products.index');
Route::get('products/{product}','ProductsController@show')->name('products.show');
