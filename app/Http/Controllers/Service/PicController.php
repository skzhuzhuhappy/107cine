<?php

namespace App\Http\Controllers\Service;

use App\Models\M3Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PicController extends Controller
{
    public function index(){
        echo "pic index";
    }
    public function store(){
        echo "pic store";
    }
    public function show(){
        echo "pic show";
    }

    /*
     * 根据 v_type 获得动态
     * job ruige job_banner activity ppxy bolex a7r filmtools wenda homebanner ppxy_newroom ppxy_newstream ppxy_home ppxy_home_mobile
     * */
    public function getPicVType($v_type,$limit){
        $data = (new \App\Models\Pic())->getbytype($v_type,$limit);
        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 200;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }
}
