<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyDicroom extends Model
{
    protected $table = 'ppxy_dicrooms';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;

    /*
     * 课程详情页
     * */
    public function dicroom_common($room){

        /*
        * 试听课程
        * */
        (new \App\Models\PpxyVideoAdd())->st_class();



        //报名价格
        $train_ids = array(5, 35);              //  过山车课程
        $video_ids = array(35);                     //  直接进入视频频道学习课程
        if ( in_array($this->room_id, $train_ids) ) {
            $view = 'ppxy/fee_train';

        }
        else {
            $view = 'ppxy/fee_normal';
        }

        //试听课程
        $filter = array('room_id'=>$this->room_id, 'type'=>'shiting');
        $adds = \App\Models\PpxyVideoAdd::where($filter)->orderby('n_weight','asc')->get();
        if ( count($adds) > 0 ) {
            foreach ($adds as $key => $add) {
                $data[$key]['poly_id']=$add->poly_id;
            }
        }

        //课程介绍模块,获取课程介绍数据
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'introduction'
        );
        $introduction = \App\Models\PpxyDicroom::where($filter)->first();
        $content=$introduction->title;
        $text=$introduction->desc;


        //学习方法模块
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'learn_method'
        );
        $learn_methods = \App\Models\PpxyDicroom::where($filter)->orderby('n_weight','asc')->get();
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'learn_method_bg'
        );

        $learn_methods_bg = \App\Models\PpxyDicroom::where($filter)->first();
        if(!empty($learn_methods_bg)){
            $learn_methods_bg_url = $learn_methods_bg->background;
        }else{
            $learn_methods_bg_url= '';
        }

        if(count($learn_methods) > 0){
            $url=$learn_methods_bg_url;

            foreach($learn_methods as $learn_method){

                if(!empty($learn_method->title)){
                    $title=$learn_method->title;
                }

                $desc=$learn_method->desc;
            }
        }


    }




    /*
     * 背景图片
     * */
    public function top_pic_url(){
        //获取top模块背景图片地址
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'top_pic'
        );
        $top_pic =\App\Models\PpxyDicroom::where($filter)->first();
        if(!empty($top_pic)){
            return  $top_pic->background;
        }else{
            return  '';
        }
    }

    /*
     * 课程亮点
     * */
    public function high_light{
        $filter = array(
            'room_id'=>$this->room_id,
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
     * 课程评论
     * */
    public function comment_room_id($room_id){
        //获取学员评价相关数据
        $filter = array(
            'room_id'=>$this->room_id,
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
     * 导师介绍
     * */
    public function teacher_room_id($room_id){
        //获取导师信息
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'teacher_info'
        );
        $teachers = $this->where($filter)->select('title','desc','background')->orderby(array('n_weight'=>'asc'))->get();
        if(count($teachers) > 0 ){
            return $teachers;
        }else{
            return array();
        }
    }


    /*
     * 机构动态模块
     * */
    public function org_room_id(){
        //机构动态模块
        $filter = array(
            'room_id'=>$this->room_id,
            'type'=>'org_affairs'
        );
        $org_affairs =  $this->->where($filter)->select('ex_info','title','desc')->orderby(array('n_weight'=>'asc'))->get();

        if(count($org_affairs) > 0) {
            $i = 1;
            $total = count($org_affairs);

            /*foreach ($org_affairs as $org_affair) {
                if ($i < 4) {
                    echo '<div class="item">';
                    echo '<span class="num">' . $org_affair->ex_info . '</span>';
                    echo '<dl><dt>' . $org_affair->title . '</dt><dd>' . $org_affair->desc . '</dd></dl>';
                    echo '</div>';
                } elseif ($i == 4) {
                    echo '<div class="more">';
                    echo '<span>显示全部动态</span>';
                    echo '<div class="item">';
                    echo '<span class="num">' . $org_affair->ex_info . '</span>';
                    echo '<dl><dt>' . $org_affair->title . '</dt><dd>' . $org_affair->desc . '</dd></dl>';
                    echo '</div></div>';
                } elseif ($i==$total-1) {
                    echo '<div class="item no-border">';
                    echo '<span class="num">' . $org_affair->ex_info . '</span>';
                    echo '<dl><dt>' . $org_affair->title . '</dt><dd>' . $org_affair->desc . '</dd></dl>';
                    echo '</div>';
                }
            }*/

            return $org_affairs;
        }else{
            return array();
        }
    }


}
