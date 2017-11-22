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


Route::get('/', 'StaticPagesController@home');
Route::get('/help', 'StaticPagesController@help');
Route::get('/about', 'StaticPagesController@about');

Route::get('/ppxy/banner_main', 'Service\PpxyController@banner_main');

Route::group(['prefix' => 'service'], function () {
    Route::get('ppxy/banner_main', 'Service\PpxyController@banner_main');

    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::post('validate_phone/send', 'Service\ValidateController@sendSMS');
    Route::post('upload/{type}', 'Service\UploadController@uploadFile');

    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');

    Route::get('category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}', 'Service\CartController@addCart');
    Route::get('cart/delete', 'Service\CartController@deleteCart');

    Route::post('alipay', 'Service\PayController@aliPay');
    Route::post('wxpay', 'Service\PayController@wxPay');

    Route::post('pay/ali_notify', 'Service\PayController@aliNotify');
    Route::get('pay/ali_result', 'Service\PayController@aliResult');
    Route::get('pay/ali_merchant', 'Service\PayController@aliMerchant');

    Route::post('pay/wx_notify', 'Service\PayController@wxNotify');
});
