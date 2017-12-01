<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MainsController extends Controller
{

                /*
     * 接口列表
     * */
    public function index(){
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


    }


}
