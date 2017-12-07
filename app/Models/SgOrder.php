<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgOrder extends Model
{
    protected $table = 'sg_orders';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;


    //付定金人数
    public function ding_count($room_id)
    {
        $filter = array('sg_product_id'=>$this->ding_id, 'v_type'=>'success');
        $ding_count = $this::where($filter)->count();
        return $ding_count;
    }

    //爬坡阶段付款人数
    public function up_count($room_id)
    {

        $filter = array(
            'sg_product_id'=>$this->product->id,
            'v_type'=>'success',
            'orderamount>='=>0,
            'd_time >'=>$this->up_start_time
        );
        $count = $this::where($filter)->count();
        return $count;
    }

    /*
     * 我的订单
     * */
    public function  sgorderMy($member_id){
        $ids = (new \App\Models\Classroom())->get_product_ids();

        $rules = \App\Models\PpxyRule::all();
        foreach ($rules as $key => $rule) {
            $ids[] = $rule->ding_id;
        }
        /*
         * 需要修改
         * */

        $orders = $this->where(array('member_id'=>$member_id, 'is_del'=>'N'))
            ->whereIn('sg_product_id',$ids)->orderBy('id','desc')->get();
        $data = array();

        foreach ($orders as $key => $order) {
            $product = \App\Models\MallProduct::find($order->sg_product_id);
            $url = 'ppxyorder/info/'.$order->id;

            $create_time = strtotime($order->d_time);
            $now_time = time();
            $diff = $now_time - $create_time;
            $expired = ($order->sg_product_id == 4311) ? 30 * 86400 : 86400;
            $not_expired = ( $diff > $expired ) ? false : true;

            if ($order->v_type == 'success') {
                $state = '已付款';
            }
            elseif ($not_expired) {
                $state = '未付款';
            }
            else {
                $state = '已过期';
            }

            if ( $order->v_type == 'ing' and $not_expired ) {
                $continue_text = '<a href="ppxyorder/pay/'.$order->id.'" target="_blank" class="pay">继续支付</a>';
            }
            else {
                $continue_text = '';
            }
            $data[$key]['d_time'] = $order->d_time;
            $data[$key]['nmb'] = $order->id;
            $data[$key]['img_src'] = $product->v_pic;
            $data[$key]['name_href'] = $url;
            $data[$key]['name_value'] = $product->v_title;
            $data[$key]['state'] = $state;
            $data[$key]['money_price'] = $order->orderamount;
            $data[$key]['money_value'] = $continue_text;

        }

        return $data;
    }


}
