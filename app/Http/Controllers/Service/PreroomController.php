<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PreroomController extends Controller
{
    /*
     * 列表 所有
     * */
    public function index(){

    }
    /*
     * 线下课
     * 根据room_id 获得详情
     * */
    public function getPreRoomByRoomId($room_id){
        return ( new \App\Models\PpxyPreroom())->find_first($room_id);
    }

}
