<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyRule extends Model
{
    protected $table = 'ppxy_rules';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    public $room_id;
    public $product;
    public $ding_product;
    public $room;
  //初始化
    public function inits($room_id)
    {
        $this->room_id = $room_id;
        $this->where(array('room_id'=>$this->room_id))->first();

        $this->room         = \App\Classroom::find($this->room_id);
        $this->product      = ORM::factory('mall_product')->find($this->room->product_id);
        $this->ding_product = ORM::factory('mall_product')->find($this->ding_id);

        return $this;
    }

}
