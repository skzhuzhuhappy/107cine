<?php

namespace App\Http\Controllers\Service;

use App\Http\Resources\ClassroomCollection;
use App\Http\Resources\CommonCollection;
use App\Models\Classroom;
use App\Models\M3Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpParser\Node\Stmt\Class_;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $data = (new Classroom())->index($input);
        return new CommonCollection($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data = (new Classroom())->show($id);
        return new \App\Http\Resources\Common($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
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
