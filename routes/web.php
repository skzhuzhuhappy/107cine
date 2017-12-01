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
Route::get('/', 'Service\MainController@index');
Route::get('/ppxy/index', 'Service\PpxysController@index');
Route::get('/ppxy/main_info', 'Service\PpxysController@main_info');
Route::get('/ppxy/find_json', 'Service\PpxysController@find_json');
Route::get('/ppxy/platform/{type}', 'Service\PpxysController@platform');
Route::get('/ppxy/course/{main_id}/{sub_id}','Service\PpxysController@course', function ($main_id,$sub_id) {

})->where(['main_id' => '[0-9]+', 'sub_id' => '[0-9]+']);

