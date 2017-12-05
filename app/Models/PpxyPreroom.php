<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyPreroom extends Model
{
    protected $table = 'ppxy_prerooms';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    /*
    * 查询一条数据
    **/
    public static function find_first($where=array(),$data='*'){
        return self::where($where)->select($data)->first();
    }

    /*
    * 获得列表
    **/
    public static function find_list($where=array(),$data='*'){
        return self::where($where)->select($data)->get();
    }

}
