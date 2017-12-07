<?php

namespace App\Http\Controllers\Service;

use App\Http\Resources\ClassroomCollection;
use App\Http\Resources\CommonCollection;
use App\Models\Classroom;
use App\Models\Job;
use App\Models\M3Result;
use App\Models\PpxyBaseMenu;
use App\Models\PpxyDicroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Queue\Failed\NullFailedJobProvider;
use PhpParser\Node\Stmt\Class_;

class PpxybasemenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = (new PpxyBaseMenu())->index($request->all());
        return new CommonCollection($data);
    }

    /**
     * Show the form for creating a new resource. 返回新建页面
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
    public function store(Request $request, Job $job)
    {
        $job->fill($request->all());
        $job->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data = (new PpxyBaseMenu())->show($id);
        return new \App\Http\Resources\Common($data);
    }

    /**
     * Show the form for editing the specified resource. 返回编辑页面
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








}
