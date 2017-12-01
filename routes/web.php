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
Route::get('/ppxy/index', 'Service\PpxyController@index');
Route::get('/ppxy/main_info', 'Service\PpxyController@main_info');
Route::get('/ppxy/find_json', 'Service\PpxyController@find_json');
Route::get('/ppxy/platform/{type}', 'Service\PpxyController@platform');

Route::get('/ppxy/course/{main_id}/{sub_id}','Service\PpxyController@course', function ($main_id,$sub_id) {

})->where(['main_id' => '[0-9]+', 'sub_id' => '[0-9]+']);

