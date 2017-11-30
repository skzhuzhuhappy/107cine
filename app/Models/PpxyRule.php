<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyRule extends Model
{
    protected $table = 'ppxy_rules';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    public $member_id;
    public $room_id;
    public $product;
    public $ding_product;
    public $room;

    public function __construct()
    {
        //parent::__construct();
        //$this->member_id = Session::instance()->get('member_id');
    }
    //初始化
    public function inits($room_id)
    {
        $this->room_id = $room_id;
        $this->where(array('room_id'=>$this->room_id))->first();

        $this->room         = \App\Models\Classroom::find($this->room_id);
        $this->product      = \App\Models\MallProduct::find($this->room->product_id);
        $this->ding_product = \App\Models\MallProduct::find($this->ding_id);

        return $this;
    }

}
