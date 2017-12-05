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

Route::resource('pics', 'Service\PicController',
    ['only' => [
        'index' , 'store' , 'show'
    ]]
);

//Route::get('classrooms/insert', 'Service\ClassroomController@insert');

Route::group(['prefix' => 'service'], function () {
    /*
     * 课程相关
     * */
    //首页数据
    Route::get('mains/index', 'Service\MainController@index');
    //根据main_id 和 sub_id获得课程
    Route::get('classrooms/main_id/{main_id}', 'Service\ClassroomController@getClassRoomByMainId');
    Route::get('classrooms/main_id/{main_id}/sub_id/{sub_id}', 'Service\ClassroomController@getClassRoomByMainIdSubId');
    //根据类型获得课程 if_online，if_star，if_free，if_home，if_course，if_train
    Route::get('classrooms/type/{type}', 'Service\ClassroomController@getClassRoomByType');
    //会员自己的课程
    Route::get('classrooms/myroom', 'Service\ClassroomController@getMyRoom');
    //下线课程 详情
    Route::get('prerooms/room_id/{room_id}', 'Service\PreroomController@getPreRoomByRoomId');
    //栏目相关
    Route::get('generaltype/index', 'Service\GeneraltypeController@index');
    Route::get('generaltype/main_id/{main_id}/sub_id/{sub_id}', 'Service\GeneraltypeController@getGeneralTypeByMainIdSubId');
    //我的订单
    Route::get('sgorder/my', 'Service\SgorderController@sgorderMy');

    //图片
    Route::get('pics/vtype/{v_type}/limit/{limit}', 'Service\PicController@getPicVType');

    //
    Route::post('pay/wx_notify', 'Service\PayController@wxNotify');
});

Route::resource('service/classrooms', 'Service\ClassroomController', ['only' => [
        'index' , 'store' , 'show'
    ]]
);

Route::get('/user', function () {
    return new \App\Http\Resources\Admin(\App\Models\Admin::paginate(3));
});
Route::get('/users', function () {
    return new \App\Http\Resources\ClassroomCollection(\App\Models\Admin::paginate());
});



