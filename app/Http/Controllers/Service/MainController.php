<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MainController extends Controller
{

                /*
     * 接口列表
     * */
    /*public function index(){
        $data =  [
            '拍片学院首页'=>'107cine.app/ppxy/index',
            '课程详情'=>'107cine.app/ppxy/dicroom',
            '课程库'=>'107cine.app/ppxy/course',
            '个人中心'=>'107cine.app/ppxy/platform',
            '订单列表'=>'107cine.app/ppxy/platform/order',
        ];
        echo '<pre>';
        $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $data = str_replace("\\/", "/",$data);
        $data = str_replace("{", "{<br/>&nbsp;&nbsp;",$data);
        $data = str_replace("}", "<br/>}&nbsp;&nbsp;",$data);
        $data = str_replace(",", ",<br/>&nbsp;&nbsp;",$data);
        echo $data;


    }*/

    public function index(){
        $data = array();
        //banner
        $banner=Pic::main_banner();
        $data['banner']=$banner;

        //var_dump($banner);
        //课程类型 数量
        $generaltypes = \App\Models\GeneralType::type_num();
        $data['generaltypes']=$generaltypes;
        //var_dump($generaltypes);
        //最新课程
        $newrooms = \App\Models\Pic::getnewrooms();
        $data['newrooms']=$newrooms;
        //var_dump($newrooms);

        //最新动态
        $newstreams = \App\Models\Pic::getnewstreams();
        $data['newstreams']=$newstreams;
        //var_dump($newstreams);
        exit;
        //你可能需要的课程
        $classromm = new \App\Models\Classroom();
        $rooms=$classromm->maybe_need_rooms();
        $data['rooms']=$rooms;

        //职业机会
        $job = new \App\Models\Job();
        $ids_one = array(18288,10560,18378);
        $tab_one=$job->getJobList($ids_one);
        $data['tab_one']=$tab_one;
        $ids_two = array(18706, 22513, 21676);
        $tab_two=$job->getJobList($ids_two);
        $data['tab_two']=$tab_two;
        $ids_three = array(20997, 14429, 12946);
        $tab_three=$job->getJobList($ids_three);
        $data['tab_three']=$tab_three;
        $ids_four = array(22498, 21989, 10903);
        $tab_four=$job->getJobList($ids_four);
        $data['tab_four']=$tab_four;


        //组装数
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();

    }


}
