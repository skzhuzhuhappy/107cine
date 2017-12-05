<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GeneraltypeController extends Controller
{
    /*
     * 栏目列表
     * */
    public function index(){
        $data = ( new \App\Models\GeneralType())->CateTree(1);
        //var_dump($data);
    }
    /*
     *  根据 main
     * */
    public function getGeneralTypeByMainIdSubId($main_id,$sub_id){
        $main_id=10;
        $sub_id=129;
        $data=( new \App\Models\GeneralType())->course_nav($sub_id,$main_id);

        //组装数
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }

    /*
     * 获得类型 数量
     * */
    public function typeNum(){
        $data=\App\Models\GeneralType::type_num();
        //组装数
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }



}
