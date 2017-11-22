<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'ppxy_classrooms';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    protected $product_ids = array();
    protected $ym, $next_ym;

    /*
    * 查询一条数据
    **/
    public static function find_first($where=array(),$data='*'){
        return self::where($where)->select($data)->first()->toArray();
    }


    /*
    *
    *可能需要的课程
    **/
    public function maybe_need_rooms(){

        $rooms = $this->homerooms();

        foreach ($rooms as $key => $room) {
          $filter_student = array('classroom_id'=>$room['id'], 'from_type'=>'fee');
          $count_student  = \App\Models\PpxyStudent::find($room['id'])->count();

          if ($room->id == 35) {
              $rule = \App\Models\ppxy_rule::inits($room['id']);
              $count_student = $rule->up_count() + $rule->ding_count();  //参与总人数
          }

          if ($room->if_online == 'N') {
              $url = 'ppxy/preroom/'.$room['id'];
              $lable = '';
              $type_name = '线下实训';
          }
          else {
              $url = 'ppxy/dicroom/'.$room['id'];
              $lable = $count_student ? $count_student.'人学习' : '';
              $type_name = '系统化';
          }

          if ( $room->if_star == 'Y' ) {
              $room->url=   '<img src="front/images/ppxy2/index/star2.png" class="start">';
          }else{
              $rooms[$key]['url']= $url;
              $rooms[$key]['nmb']= $lable;
              $rooms[$key]['type']= $type_name;
          }
          return $rooms;
       }
    }

    /*
	和学院相关的产品ID
	*/
	public function get_product_ids()
	{
		$rooms = $this->where(array('is_del'=>'N'))->get();
		foreach ($rooms as $key => $value) {
			if (!empty($value->product_id)) {
				$this->product_ids[] = $value->product_id;
			}
			if (!empty($value->renew_product_id)) {
				$this->product_ids[] = $value->renew_product_id;
			}
		}
		$this->product_ids[] = 4050;
		$this->product_ids[] = 3657;
		$this->product_ids[] = 4192;
		$this->product_ids[] = 4193;
		$this->product_ids[] = 4194;
		$this->product_ids[] = 4267;
		$this->product_ids[] = 4268;
		$this->product_ids[] = 4266;
		return $this->product_ids;
	}


	//获取某月排名记录
	public function sall($room_id, $y, $m)
	{
		$this->id = $room_id;
		$this->ym = cine::get_ym($y, $m);
		$this->next_ym = cine::get_ym($y, $m, true);
		// var_dump($this->id, $this->ym, $this->next_ym);
		// return;
		if ( $this->if_ping() ) {
			$filter = array('from_type'=>'fee', 'classroom_id'=>$this->id);
			$students = ORM::factory('ppxy_student')->where($filter)->find_all();
			foreach ($students as $key => $student) {
				if ($student->member_id != 0 and $student->member_id < 800000) {
					$totalscore = $this->sone( $student->member_id );
					$data = array('room_id'=>$this->id,'member_id'=>$student->member_id,'allscore'=>$totalscore,'year_month'=>$this->ym);
					ORM::factory('ppxy_rank_log')->add($data);
					echo $student->realname, ' ', $student->member_id, ' ', $totalscore;
					echo '<br />';
				}
			}
			$this->setrank();
		}
	}

	//是否已评价完
	protected function if_ping()
	{
		$count = \App\PpxyRankLog::where(array('room_id'=>$this->id,'year_month'=>$this->ym))->count();
    exit;
		if ($count > 0) {
			echo "$this->id $this->ym 已经添加";
			return false;
		}
		$sql = "SELECT COUNT(*) cum   FROM `ppxy_answers` pa LEFT JOIN `ppxy_tasks` pt  on pt.`id` = pa.`task_id`
		WHERE pt.`room_id` = $this->id AND pa.`d_time` < '$this->next_ym' AND pa.`n_score` =0";
		$query= DB::select($sql);
		$count = $query[0]->cum;
		if ($count > 0) {
			echo $room_id.' '.$this_ym.'还有 '.$count.' 作业没有点评';
			return false;
		}
		else{
			echo '所有作业已经点评';
			return true;
		}
	}


	//获取单个人总积分
	public function sone( $member_id )
	{
		$filter = array('room_id'=>$this->id);
		$tasks = ORM::factory('ppxy_task')->where($filter)->find_all();
		$allscore = 0;
		foreach ($tasks as $key => $task) {
			$high = $this->shigh($task->id, $member_id);
			$allscore += $high;
		}
		return $allscore;
	}

	//获取单个人某作业最高分
	protected function shigh($task_id, $member_id)
	{
		$high = 0;
	    $filter = array('task_id'=>$task_id,'member_id'=>$member_id,'d_time<'=>$this->next_ym);
	    $answers = ORM::factory('ppxy_answer')->where( $filter )->select('n_score')->find_all();
	    foreach ($answers as $key => $value) {
	    	$high = ($value->n_score > $high) ? $value->n_score : $high;
	    }
	    return $high;
	}

	public function set_next_ym($ym)
	{
		$this->next_ym = $ym;
	}

	//对某月排名
	public function setrank()
	{
		$filter = array('room_id'=>$this->id, 'year_month'=>$this->ym);
		$students = ORM::factory('ppxy_rank_log')->where($filter)->orderby(array('allscore'=>'desc'))->find_all();
		$pre = 0;
		$rank = 1;
		$real = 1;
		foreach ($students as $key => $student) {
			if ($student->allscore < $pre) {
				$rank = $real;
			}
			$student->rank = $rank;
			$student->save();
			$pre = $student->allscore;
			$real++;
		}
	}


	// 纯视频课程的首地址
	public function firstnode($room_id=0)
	{
		if (empty($room_id)) {
			$room_id = $this->id;
		}

        $data = new Database;
        $sql = 'select pn.id node_id, pm.n_weight, pn.n_weight from ppxy_nodes pn left join ppxy_base_menus pm on pm.id = pn.menu_id where pn.classroom_id='.$room_id.' and pm.n_weight is not null order by pm.n_weight asc, pn.n_weight asc limit 1 ';
        $query = $data->query($sql);
        $firstid = $query[0]->node_id;

        if (empty($firstid)) {
        	$url = 'ppxy/dicroom/'.$room_id;
        }
        else {
        	$url = 'ppxy/node/'.$room_id.'/'.$firstid;
        }
        return $url;
	}

	// 首页课程
	public function homerooms()
	{
      return $this->where(array('if_home'=>'Y'))->orderBy('n_weight','asc')->get()->toArray();
	}

	// 课程库课程
	public function courserooms($filter=array())
	{
		$filter['if_course'] = 'Y';
		return $this->where($filter)->orderby(array('n_weight'=>'asc'))->find_all();
	}

	// 课程价格
	public function price($room_id = 0)
	{
		if (!empty($room_id)) {
			$this->find($room_id);
		}
		elseif (empty($this->id)) {
			return 0;
		}
		$price = ORM::factory('mall_product')->price($this->product_id);
		return $price;
	}

	// 经销商优惠价格
	public function discount_rate($code, $room_id = 0)
	{
		$ticket = ORM::factory('ppxy_ticket')->where(array('code'=>$code))->find();
		if (empty($ticket->id)) {
			return 0;
		}
		$link = ORM::factory('room_code_link')->where(array('code_id'=>$ticket->id))->find();
		return $link->discount_rate;
	}

	// 经销商课程box数据
	// 默认使用 $this->id
	public function one($code, $room_id = 0)
	{

		if (!empty($room_id)) {
			$this->find($room_id);
		}
		elseif (empty($this->id)) {
			return array();
		}
		$count = ORM::factory('ppxy_student')->count($this->id);
		if ($this->id == 35) {
		    $rule 	= ORM::factory('ppxy_rule')->inits($this->id);
		    $count 	= $rule->up_count() + $rule->ding_count();  //参与总人数
		}

		$price = $this->price();

		$discount_rate = $this->discount_rate($code);
		$dis_price = round($price * $discount_rate);

		$data    = array(
			'id'			=>$this->id,
			'title' 		=>$this->v_title,
			'pic'			=>$this->pre_pic,
			'teacher_name'	=>$this->teacher_name,
			'if_star'		=>$this->if_star,
			'count'			=>$count,
			'price'			=>$price,
			'dis_price'		=>$dis_price
		);
		return $data;
	}

}
