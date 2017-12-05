<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SgorderController extends Controller
{

     /*
     *
     * */
    public function index(){

    }
    /*
     * 我的订单
     * */
    public function mySgorder(){
        $member_id=846933;
        $data=( new \App\Models\SgOrder())->my_order($member_id);
        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }


}
