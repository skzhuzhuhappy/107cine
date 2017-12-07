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
Route::get('/ppxy/course/{main_id}/{sub_id}', 'Service\PpxysController@course', function ($main_id, $sub_id) {


})->where(['main_id' => '[0-9]+', 'sub_id' => '[0-9]+']);

//Route::get('classrooms/insert', 'Service\ClassroomController@insert');

Route::group(['prefix' => 'api'], function () {
    Route::get('generaltypes/getmenu', 'Service\GeneralTypeController@getmenu');

    //首页数据
    Route::get('mains/index', 'Service\MainController@index');

    //课程表
    Route::resource('classrooms', 'Service\ClassroomController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //目录
    Route::resource('generaltypes', 'Service\GeneralTypeController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    // 各种 banner
    Route::resource('pics', 'Service\PicController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //招聘表
    Route::resource('jobs', 'Service\JobController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //会员公司表
    Route::resource('companymembers', 'Service\CompanymemberController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //宣传页信息表
    Route::resource('dicrooms', 'Service\PpxydicroomController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //试听视频表
    Route::resource('videoadds', 'Service\PpxyvideoaddController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //课程大纲
    Route::resource('basemenus', 'Service\PpxybasemenuController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //帖子类型
    Route::resource('nodes', 'Service\PpxynodeController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //课程问题
    Route::resource('tasks', 'Service\PpxynodeController', ['only' => [
            'index', 'store', 'show'
        ]]
    );
    //课程问题答案
    Route::resource('answers', 'Service\PpxyanswerController', ['only' => [
            'index', 'store', 'show'
        ]]
    );

    //课程问题
    Route::resource('tasks', 'Service\PpxytaskController', ['only' => [
            'index', 'store', 'show'
        ]]
    );

});





