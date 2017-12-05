<?php

namespace App\Http\Controllers\Service;

use App\Models\M3Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClassroomController extends Controller
{

    public function index(){
        echo "classroom index";
    }
    public function store(){
        echo "classroom store";
    }
    public function show($room_id){

        echo "classroom show";
    }
    public function create(){
        echo 'classroom create';
    }
    public function insert(){
        echo 'classroom insert';
    }
    public function build(){
        echo 'classroom build';
    }

    /*
     * 课程库
     * */
    public function getClassRoomByMainIdSubId($main_id,$sub_id){
        $data=( new \App\Models\Classroom())->find_rooms_sub_id_main_id($main_id,$sub_id);
        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }

    public function getClassRoomByMainId($main_id){
        $data=( new \App\Models\Classroom())->find_rooms_main_id($main_id);
        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }

    /*
     * 类型 课程
     * */
    public function getClassRoomByType($type){
        //根据类型 获得课程
        $data['star_rooms']=( new \App\Models\Classroom())->room_type($type);

        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }

    /*
     * 我的课程
     * */
    public function getMyRoom(){
        $member_id=846933;
        $data=( new \App\Models\Classroom())->myclass_romm($member_id);

        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }


}
