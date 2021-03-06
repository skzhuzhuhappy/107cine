<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyDicroom extends Model
{
    protected $table = 'ppxy_dicrooms';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;

    protected $room_id;

    /*
    * index
    * */
    public function index($data)
    {
        //过滤查询条件
        $pageSize = 10;
        $page = 1;
        $filter = $this->zf_search($data);
        $select = "*";
        $order = 'n_weight';
        $by = 'asc';
        if (isset($data['per_page']) && $data['per_page']) {
            $pageSize = $data['per_page'];
        }
        if (isset($data['page']) && $data['page']) {
            $page = $data['page'];
        }
        if (isset($data['order']) && $data['order'] && isset($data['by']) && $data['by']) {
            $order = $data['orderby'];
            $by = $data['by'];
        }
        if (isset($data['select']) && $data['select']) {
            $select = explode(',', $data['select']);
        }
        $list = $this->where($filter)->select($select);

        $res = $list->orderBy($order, $by)->paginate($pageSize);


        return $res;
    }

    /*
     * 组装查询条件
     * */
    public function zf_search($data)
    {
        $filter = array();
        if (is_array($data) && $data) {
            if (isset($data['room_id']) && $data['room_id']) {
                $filter['room_id'] = $data['room_id'];
            }
            if (isset($data['type']) && $data['type']) {
                $filter['type'] = $data['type'];
            }
        }
        return $filter;
    }

    /*
     * 获得一条
     * */
    public function show($id)
    {
        $res = $this->find($id);
        return $res;
    }


    /*
     * 课程详情页
     * */
    public function dicroom_common($room_id){
        $data = array();
        if($room_id){
            $this->room_id = $room_id;
        }
        //获取top模块背景图片地址
        $data['top'] = $this->top_pic_url($this->room_id);

        /*
        * 试听课程
        * */
        $data['shiting'] = (new \App\Models\PpxyVideoAdd())->st_class($this->room_id);

        //课程亮点模块
        $data['high_light'] = $this->high_light($this->room_id);

        //报名价格



        //课程介绍模块,获取课程介绍数据
        $data['introduction'] = $this->introduction($this->room_id);


        //学习方法模块
        $data['learn_methods'] = $this->learn_methods($this->room_id);

        //课程大纲
        $data['base_menu_room_id'] = (new \App\Models\PpxyBaseMenu())->base_menu_room_id($this->room_id);
        //(new \App\Models\PpxyBaseMenu())->menu_info_room_id($this->room_id);

        //获取学员评价相关数据
        $data['comment_room_id'] = $this->comment_room_id($this->room_id);


        //获取导师信息
        $data['teacher_room_id'] = $this->teacher_room_id($this->room_id);

        //机构动态模块
        $data['org_room_id'] = $this->org_room_id($this->room_id);


    }




    /*
     * 背景图片
     * */
    public function top_pic_url($room_id){
        //获取top模块背景图片地址
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'top_pic'
        );
        $top_pic =\App\Models\PpxyDicroom::where($filter)->first();
        if(!empty($top_pic)){
            return  $top_pic->background;
        }else{
            return  '';
        }
    }

    //课程介绍模块,获取课程介绍数据
    public function introduction($room_id){
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'introduction'
        );
        $introduction = \App\Models\PpxyDicroom::where($filter)->first();
        $data = array();
        $data['content']=$introduction->title;
        $data['text']=$introduction->desc;

        return $data;
    }

    //学习方法模块
    public function learn_methods($room_id){
        //学习方法模块
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'learn_method'
        );
        $learn_methods = \App\Models\PpxyDicroom::where($filter)->orderby('n_weight','asc')->get();
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'learn_method_bg'
        );

        $learn_methods_bg = \App\Models\PpxyDicroom::where($filter)->first();
        if(!empty($learn_methods_bg)){
            $learn_methods_bg_url = $learn_methods_bg->background;
        }else{
            $learn_methods_bg_url= '';
        }
        $data = array();
        if(count($learn_methods) > 0){
            $data['url']=$learn_methods_bg_url;

            foreach($learn_methods as $learn_key => $learn_method){

                if(!empty($learn_method->title)){
                    $data[$learn_key]['title']=$learn_method->title;
                }
                $data[$learn_key]['text']=$learn_method->desc;
            }
        }
        return $data;
    }
    /*
     * 课程亮点
     * */
    public function high_light($room_id){
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'highlight'
        );
        $highlights = \App\Models\PpxyDicroom::where($filter)->limit(2)->orderby('n_weight','asc')->get();
        $data=array();
        if(!empty($highlights)){
            foreach($highlights as $k => $highlight){
                $data[$k]['title']=$highlight->title;
                $data[$k]['description']=$highlight->desc;
            }
        }
        return $data;
    }



    /*
     * 获取学员评价相关数据
     * */
    public function comment_room_id($room_id){

        $filter = array(
            'room_id'=>$room_id,
            'type'=>'st_comment'
        );
        $st_comments =$this->where($filter)->select('desc','title','ex_info')->orderby(array('n_weight'=>'asc'))->get();
        if(count($st_comments) > 0 ){
            return $st_comments;
        }else{
            return array();
        }
    }

    /*
     * //获取导师信息
     * */
    public function teacher_room_id($room_id){
        $filter = array(
            'room_id'=>$room_id,
            'type'=>'teacher_info'
        );
        $teachers = $this->where($filter)->select('title','desc','background')->orderby(array('n_weight'=>'asc'))->get();
        if(count($teachers) > 0 ){
            return $teachers;
        }else{
            return array();
        }
    }





}
