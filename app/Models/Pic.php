<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    protected  $table = 'pics';
    protected  $primaryKey = 'id' ;
    public  $timestamps = false;

    /*
     * index
     * */
    public function index($data){
        //过滤查询条件
        $pageSize = 10;
        $page = 1;
        $filter = $this->zf_search($data);
        $order = 'n_weight';
        $by = 'asc';


        if(isset($data['per_page'])&&$data['per_page']){
            $pageSize = $data['per_page'];
        }
        if(isset($data['page'])&&$data['page']){
            $page = $data['page'];
        }
        if(isset($data['order'])&&$data['order']&&isset($data['by'])&&$data['by']){
            $order = $data['order'];
            $by = $data['by'];
        }
        if(isset($data['limit'])&&$data['limit']){
            $res = $this->where($filter)->orderBy($order,$by)->limit($data['limit'])->get();
        }else{
            $res = $this->where($filter)->orderBy($order,$by)->paginate($pageSize);
        }
        return $res;
    }

    /*
     * 组装查询条件
     * */
    public function zf_search($data){
        $filter = array();
        if(is_array($data)&&$data){
            if(isset($data['v_type'])&&$data['v_type']){
                $filter['v_type'] = $data['v_type'];
            }
        }
        return $filter;
    }

    /*
     * 获得一条
     * */
    public function show($id){
        $res = $this->find($id);
        return $res;
    }

    /*
     * 首页 banner  if_mobile
     * */
    public static function main_banner($if_mobile='web'){
      if('mobile'==$if_mobile ){
        $where=array('v_type'=>'ppxy_home_mobile');
      }else{
        $where=array('v_type'=>'ppxy_home');
      }
      $banner =self::where($where)
          ->orderBy('n_weight', 'asc')
          ->pluck('v_url', 'v_pic')->toArray();
      return $banner;
    }


    /*
      *最新课程
      *
      */
    public static function getnewrooms(){
        $newrooms=self::getbytype($type='ppxy_newroom',$limit='3');
        foreach ($newrooms as $key => $newroom) {
          $room =\App\Models\Classroom::where(array('id'=>$newroom['rel_id']))
          ->select('id','v_title','teacher_name','if_online')
          ->first()
          ->toArray();
          $newrooms[$key]['url'] = ($room['if_online'] == 'Y') ? 'ppxy/dicroom/'.$room['id']: 'ppxy/preroom/'.$room['id'];
          $newrooms[$key]['v_title']=$room['v_title'];
          $newrooms[$key]['teacher_name']=$room['teacher_name'];
          $newrooms[$key]['room_id']=$room['id'];
        }
        return $newrooms;
    }


    /*
    *最新动态
    **/
    public static function getnewstreams(){
        $newstreams=self::getbytype($type='ppxy_newstream',$limit='4');
        foreach ($newstreams as $key => $newstream) {
          $stream =\App\Models\Stream::where(['id'=>$newstream['rel_id']])
          ->select('id','d_time','v_title')
          ->orderBy('d_time', 'desc')
          ->first();
          $newstreams[$key]['date'] = isset($stream->id) ? substr($stream->d_time, 0, 10) : substr($newstream['d_time'], 0, 10);
          $newstreams[$key]['title'] = ($newstream['v_title']) ? $newstream['v_title'] : $stream->v_title;
          $newstreams[$key]['url'] = ($newstream['v_url']) ? $newstream['v_url'] : 'stream/'.$stream->id;
        }
        return $newstreams;
    }

    /*
     * 根据 v_type 获得动态
     * */
    public static function getbytype($type='ppxy_newstream',$limit='4'){
        return self::where(array('v_type'=>$type))
        ->orderBy('n_weight', 'asc')
        ->limit($limit)
        ->get()
        ->toArray();
    }



}
