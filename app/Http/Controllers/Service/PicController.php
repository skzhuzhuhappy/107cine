<?php

namespace App\Http\Controllers\Service;

use App\Http\Resources\CommonCollection;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $data = (new Pic())->index($input);
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
