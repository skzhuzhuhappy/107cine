<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\M3Result;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PpxysController extends Controller
{
    
    public $template = 'ppxy/template';
    public $pri_actions = array(
            'node', 'classroom', 'add', 'edit', 'delnode', 
            'task', 'addtask', 'addanswer', 'add_qa_detail'
            ); //需要权限访问的action
    public $member_id; //用户id
    public $type;  //用户身份
    public $room_id; //课程id
    public $room;    //课程
    public $action; //请求action
    public $reply_id; //回复ID
    public $task_id; //作业ID

    public $if_admin = false; //是否是管理员
    public $if_student = false; //是否是学生

    public $platform_type;  //个人中心类型
    public $agree;      //是否签订保密协议

    public $post;   //提交信息
    public $agent; //客户端类型

    public $prevent_ids = array(2200, 2500, 2900, 52, 53);

    public $s1_ids = array(2, 3, 4, 5, 12, 9, 15, 22, 23, 24, 26, 25, 29, 30, 32, 35, 40, 45, 49);  //s1课堂
    public $up_home     = array(3,4,5,7,9,12,21,6,15,23,25,22,29, 32, 35, 37, 38, 39, 41, 44);  //课程库显示
    public $up_home2    = array(5,9,12,21,15,23,25,22,29, 35, 37, 38, 39, 41, 44);  //首页显示

    //板块名称
    public $stream_types = array( 
        'normal'=>'普通帖子',
        'notice'=>'动态/公告',    
        'base'  =>'基础课程',    
        'essence'=>'主题讨论',    
        // 'field' =>'技能测试',  
        // 'qa'    =>'学员问答',  
        'self'  =>'资源库',    
        'job'   =>'职业机会',  
        'read'  =>'新生必读'  
    );
    //目录
    public $menu = array(
        'home'      =>'社群首页',
        'forum'     =>'学习中心',
        'base'      =>'基础视频',
        'task'      =>'作业中心',
        'qacenter'  =>'提问中心',
        'rank'      =>'学分排行榜',
        'notices'   =>'通知中心',
        'read'      =>'新生必读',
    );

    //个人中心
    public $platform = array(
        'home'  =>'我的主页',
        'zhengshu'=>'我的证书',
        'order'  =>'我的订单',
        );

    public function __construct()
    {
        //Route::current();
        $url= Route::getFacadeRoot()->current()->uri();
        $segments=explode('/',$url);
        //$segments = Router::$segments;
        $this->segments = $segments;
        $this->room_id = intval(isset($segments[2])?$segments[2]:'0');
        $this->action = $segments[1];
        if ($this->action == 'zhibo') {
            //$this->template = 'ppxy/zhibo';
        }

        if ( $this->action != 'endroom' ) {
            //$this->template = 'ppxy/template_room';    
        }

        if (in_array($this->action, array('promotion', 'school', 'system'))) {
            //$this->template = 'template/blank';
        }

        if ( $this->action == 'csc' ) {
            //$this->template = 'ppxy/csc_template';
        }

        if ( $this->action == 'zhibo' ) {
            //$this->template = 'ppxy/zhibo_template';
        }

        $this->action = empty($this->action) ? 'index' : $this->action;
        $v2_actions = array('index', 'index2', 'dicroom', 'baselist', 'course', 'platform', 'zhengshu', 'poly_video');
        if ( in_array($this->action, $v2_actions) ) {
            //$this->template = 'ppxy/v2_template';
        }

        $form_actions = array('apply', 'apply_success', 'joinfree');
        if (in_array($this->action, $form_actions)) {
            //$this->template = 'ppxy/v2_template';
        }

        //parent::__construct();

        //$this->member_id = Session::instance()->get('member_id');
        $this->member_id = 846933;
        $this->type = $this->get_type();
        $this->agent =  $_SERVER['HTTP_USER_AGENT'];


        // var_dump($this->if_mobile);
        $agree = \App\Models\PpxyAgree::where(array('member_id'=>$this->member_id))->first();
        $this->agree = empty($agree->id) ? false : true;
        //var_dump($this->type);


        if ($this->room_id == 40) {
            if (empty($this->member_id)) {
                //url::redirect('my/login?url=ppxy/classroom/'.$this->room_id);
            }
            else if ($this->type == 'visitor') {
               //url::redirect('ppxy');
            }
        }


        //课堂权限
        if(in_array($this->action, $this->pri_actions))
        {
            if (in_array($this->room_id, $this->prevent_ids) and $this->action != 'preroom' and $this->type == 'student' and $this->member_id != 683105 and $this->action != 'dicroom') {
                url::redirect('ppxy/dicroom/'.$this->room_id);
            }
            $room = ORM::factory('ppxy_classroom')->find($this->room_id);
            if(empty($room->id))
            {
                url::redirect('ppxy/');
            }
            if( $this->type == 'visitor' )
            {
                url::redirect('ppxy/pres/'.$this->room_id.'/');   
            }

            $ifend = ORM::factory('ppxy_student')->ifend($this->room_id, $this->member_id);
            if ($ifend and $this->action != 'endroom' and $this->action != 'node') {
                // url::redirect('ppxy/endroom/'.$this->room_id.'/');
            }

            $info = array(
                'member_id' => $this->member_id,
                'action'    => $this->action,
                'room_id'   => $this->room_id,
                'member_type'   => $this->type
                );
            ORM::factory('ppxy_sitetrace')->addinfo($info);
        }

    }
  /*
   * 首页数据
   * */
    public function index(){

        $data = array();
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
        $data['tab_four']=$tab_four;

        //组装数
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();

    }

    /*
     * 课程详情
     * */
     public function dicroom($room_id)
    {
        $data = array();
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

    //课程库
     public function course($main_id,$sub_id)
    {
        // exit();
        //$this->title = '课程库';
        //$sub_id = intval($_GET['sub_id']);
        $data = array();
        $data['title'] = '课程库';
        //$main_id = intval($_GET['main_id']);
        if(empty($main_id)){
            echo url("/ppxy/index");
        }

        //$course_nav = ( new \App\Models\GeneralType())->course_nav($sub_id,$main_id);
        //$data['course_nav'] = $course_nav;
        $rooms =( new \App\Models\Classroom())->find_rooms_sub_id_main_id($sub_id,$main_id);
        $data['rooms'] = $rooms;
        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();

    }



    /*
    * 个人中心
    **/
    public function platform($type='')
    {
        if (empty($this->member_id)) {
            url::redirect('my/login?url=ppxy/platform');
        }
        $type = empty($type) ? 'home' : $type;
        $data = array();
        
        if ($type == 'order') {
            $data['title'] = '我的订单';
            $data['my_order']=( new \App\Models\SgOrder())->my_order($this->member_id);
        }else {
            $data['title'] = '我的课程';
            $data['myclass']=( new \App\Models\Classroom())->myclass_romm($this->member_id);
        }
        //热门课程
        $data['star_rooms']=( new \App\Models\Classroom())->star_rooms();

        //组装数据
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->data = $data;
        return $m3_result->toJson();
    }


  public function rooms()
   {
       $rooms = \App\Models\Classroom::homerooms();
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

    //管理员权限判断 老师，工作人员也是
    private function if_admin($room_id='')
    {
        $room_id = intval($room_id);
        $room_id = empty($room_id) ? $this->room_id : $room_id;
        $ppxy_admin = \App\Models\PpxyAdmin::where(array('member_id'=>$this->member_id))->first();

        if($ppxy_admin&&$ppxy_admin->v_type == 'admin')
        {
            $this->if_admin = true;
            return true;
        }
        else
        {
            $filter = array(
                'member_id'=>$this->member_id,
                'room_id'=>$room_id
            );
            $ppxy_pri =\App\Models\PpxyPri::where($filter)->first();
            $ppxy_pri && $this->if_admin = $ppxy_pri->id ? true : false;
            return $ppxy_pri && $ppxy_pri->id ? true : false;
        }
    }

    //学生权限判断
    private function if_student($room_id='')
    {
        $room_id = intval($room_id);
        $room_id = empty($room_id) ? $this->room_id : $room_id;
        $student =\App\Models\PpxyStudent::where(array('member_id'=>$this->member_id, 'classroom_id'=>$room_id))->first();
        $student && $this->if_student = $student->id ? true : false;
        return $student && $student->id ? true : false;
    }

    //获取用户身份
    public function get_type($room_id='')
    {
        $room_id = intval($room_id);
        $room_id = empty($room_id) ? $this->room_id : $room_id;
        if(empty($this->member_id))
        {
            return 'visitor';
        }
        if( $this->if_admin($room_id) )
        {
            $type_filter = array('member_id'=>$this->member_id);
            $type_admin =\App\Models\PpxyAdmin::where($type_filter)->first();
            return $type_admin->v_type;  //管理员权限
        }
        else
        {
            return $this->if_student($room_id) ? 'student' : 'visitor';
        }
    }



}
