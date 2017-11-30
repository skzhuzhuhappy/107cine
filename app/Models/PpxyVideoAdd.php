<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyVideoAdd extends Model
{
   	protected $table = 'ppxy_video_adds';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;
    protected $room_id;
    /*
    * 试听课程
    * */
    public function st_class($room_id){
        //有些课程有试听视频，这部分显示在头背景
        $filter = array('room_id'=>$room_id, 'type'=>'xuanchuan');
        $xuan =\App\Models\PpxyVideoAdd::where($filter)->first();
        if (!empty($xuan->id)) {
            $value='ppxyraw/video/'.$xuan->poly_id;
            $href='ppxy/dicroom/'.$room_id;
        }
        return array('value'=>$value,'href'=>$href);
    }
}
