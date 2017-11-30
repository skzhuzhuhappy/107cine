<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $table = 'streams';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    /*
    * 查询一条数据
    **/
    public static function find_first($where=array(),$data='*'){
        return self::where($where)->select($data)->first();
    }
}
