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

    //收藏与取消收藏
    Route::post('products/{product}/favorite','ProductsController@favorite')->name('products.favorite');
    Route::delete('products/{product}/favorite','ProductsController@disFavorite')->name('products.disFavorite');

    //个人收藏页面
    Route::get('products/favorite','ProductsController@favoriteShow')->name('products.favoriteShow');
});

//商品展示
Route::get('products','ProductsController@index')->name('products.index');
Route::get('products/{product}','ProductsController@show')->name('products.show');


