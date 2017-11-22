<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
error_reporting(E_ALL ^ E_NOTICE);
class PpxyController extends Controller
{
  /*
   * 首页数据
   * */
    public function main_info(){

        $data=array();
        //banner
        $banner=Pic::main_banner();
        $data['banner']=$banner;
        //var_dump($banner);
        //课程类型 数量
        $generaltypes = \App\Models\GeneralType::type_num();
        $data['generaltypes']=$generaltypes;
        //var_dump($generaltypes);
        //最新课程
        $newrooms = \App\Models\Pic::getnewrooms();
        $data['newrooms']=$newrooms;
        //var_dump($newrooms);

        //最新动态
        $newstreams = \App\Models\Pic::getnewstreams();
        $data['newstreams']=$newstreams;
        //var_dump($newstreams);

        //你可能需要的课程
        $classromm = new \App\Models\Classroom();
        $rooms=$classromm->maybe_need_rooms();
        $data['rooms']=$rooms;

        //职业机会
        $job = new \App\Models\Job();
        $ids_one = array(18288,10560,18378);
        $tab_one=$job->getJobList($ids_one);
        $data['tab_one']=$tab_one;
        $ids_two = array(18706, 22513, 21676);
        $tab_two=$job->getJobList($ids_two);
        $data['tab_two']=$tab_two;
        $ids_three = array(20997, 14429, 12946);
        $tab_three=$job->getJobList($ids_three);
        $data['tab_three']=$tab_three;
        $ids_four = array(22498, 21989, 10903);
        $tab_four=$job->getJobList($ids_four);
        $data['$tab_four']=$tab_four;

        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->categorys = $data;

        return $m3_result->toJson();

    }

     public function dicroom($room_id)
    {
        //铁粉
        // $this->pre_tf();
        $room =\App\Models\Classroom::find($room_id);
        if (empty($room->id)) {
            //url::redirect('ppxy');
        }
        //免费课程
        if ($room->if_free == 'Y') {
            //url::redirect('ppxy/joinfree/'.$room->id);
        }
        $this->template->title = $room->v_title;
        if ($this->if_mobile) {
            if ($this->room_id == 50) {
                //url::redirect('qihong');
            }
            $view = 'ppxy/dicroom_mobile';
        }else {
            $common_dicroom = array('5');
            if(in_array($this->room_id,$common_dicroom )){
                $view = 'ppxy/dicroom_common';
            }else{
                $view = 'ppxy/dicroom_'.$room->id;
            }
        }
        $this->room = $room;
        $this->template->unity = new View($view, array('room'=>$room));
        
        $openid = Session::instance()->get('openid');
        if (empty($openid) and $this->if_weixin == 'Y' and $room->id == 51) {
            url::redirect('wx/url/?state=ppxy/dicroom/51');    
        }
        // 分享红包
        if ( in_array($room->id, Kohana::config('ppxy.hongbao')) and $this->if_weixin == 'Y') {
            $openid = Session::instance()->get('openid');
            $share_code = Session::instance()->get('share_code');
            $code = $_GET['code'];

            if ( empty($openid) ) {                                         // 强制微信登录
                if (strlen($code) == 12) {              // 登录前记录跟踪码
                    Session::instance()->set(array('share_code'=>$code));    
                }
                url::redirect('wx/url/?state=ppxy/dicroom/'.$room->id);
            }
            ORM::factory('ppxy_dicroom')->add_ticket($room->id);            // 检验及为用户生成跟踪码

            if (empty($code) and !empty($share_code)) {     // 没有code时查看是有有sharecode,有就加上 
                url::redirect('ppxy/dicroom/'.$room->id.'?code='.$share_code);    
            }

            // var_dump($share_code);


            // if (!empty($openid) and empty($_GET['code'])) {
            //     $filter = array('member_id'=>$this->member_id, 'v_type'=>'normal', 'room_id'=>$room->id);
            //     $code = ORM::factory('ppxy_ticket')->where($filter)->find()->code;
            //     url::redirect('ppxy/dicroom/'.$room->id.'?code='.$code);              
            // }
        }
    }




  public function rooms()
   {
       $rooms = \App\Classroom::homerooms();
       $data = array();
       foreach ($rooms as $key => $room) {
            $filter_student = array('classroom_id'=>$room->id, 'from_type'=>'fee');
            $count_student  = ORM::factory('ppxy_student')->where($filter_student)->count_all();
            $count = ORM::factory('ppxy_node')->activeNumber($room->id); //交互次数
            $url = 'ppxy/dicroom/'.$room->id;
            $product_url = 'ppxyorder/create/'.$room->product_id;
            $product = ORM::factory('mall_product')->find($room->product_id);
            $title = str_replace('学习社群', '', $room->v_title);

            if (!empty($room->label)) {
              $label = $room->label;

            }
            elseif ($product->purchase_price > 0 && $product->n_price < $product->purchase_price) {
                $zhe = round(10*$product->n_price/$product->purchase_price,1);
                $label = $zhe.'折优惠';
            }

            $price = ORM::factory('ppxy_rule')->inits($room->id)->dis_price();

        if ($room->id == 35) {
            $price = ORM::factory('mall_product')->price(4286);
        }

      $data[] = array(
        'pic' 			=> $room->pre_pic,
        'url'			=> $url,
        'label'			=> $label,
        'product_url'	=> $product_url,
        'price'			=> $price,
        'yuan_price'	=> $product->purchase_price,
        'length'		=> $room->length,
        'title'			=> $title,
        'teacher'		=> $room->teacher_name
      );
    }
    echo json_encode($data);

   }


}
