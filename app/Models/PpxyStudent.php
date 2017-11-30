<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyStudent extends Model
{
  protected $table = 'ppxy_students';
  protected $primaryKey = 'id' ;
  public $timestamps = false;

    public function editdate($room_id, $date)
    {
        $students = $this->where(array('classroom_id'=>$room_id))->find_all();
        foreach ($students as $student) {
            $student->start_date = $date['start'];
            $student->end_date = $date['end'];
            $student->save();

        }
    }

    //是否是该班学员
    public function ifin($room_id='', $member_id='')
    {
        if (empty($room_id) or empty($member_id)) {
            return false;
        }
        $filter = array('classroom_id'=>$room_id, 'member_id'=>$member_id);
        $student = $this->where($filter)->find();
        return empty($student->id) ? false : true;
    }

    public function getsql()
    {
        var_dump($this->db->query());
    }

    public function disnumber($room_id, $member_id)
    {
        $number = $this->where(array('classroom_id'=>$room_id, 'member_id'=>$member_id))->find()->number;
        return substr(strval($number+10000),1,4);
    }
    /*
    * qa 		问题总数
    * task 		作业总数
    * tasked 	已完成
    * allscore 	总分
    */
    public function getnumber($room_id, $member_id, $type='')
    {
        $nums = array();
        if (empty($type) or $type == 'qa') {
            $filter = array('classroom_id'=>$room_id, 'member_id'=>$member_id, 'if_qa'=>'Y');
            $nums['qa'] = ORM::factory('ppxy_node')->where($filter)->count_all();
        }

        $tasks = ORM::factory('ppxy_task')->where(array('room_id'=>$room_id))->find_all();
        $nums['task'] = count($tasks);
        $nums['tasked'] = 0;
        $nums['allscore'] = 0;
        foreach ($tasks as $key => $task) {
            $high = ORM::factory('ppxy_answer')->highscore($task->id, $member_id);
            $nums['allscore'] += $high;
            if ($high > 0) {
                $nums['tasked'] += 1;
            }
        }

        if (empty($type) or $type == 'base') {
            $nums['watched'] = ORM::factory('ppxy_basetrace')->where(array('room_id'=>$room_id, 'member_id'=>$member_id))->count_all();
            $nums['base'] = ORM::factory('ppxy_node_type')->where(array('room_id'=>$room_id, 'v_type'=>'base'))->count_all();
        }


        return empty($type) ? $nums : $nums[$type];
    }

    //学员是否能访问到期页面
    public function ifend($room_id, $member_id)
    {
        if (empty($member_id)) {
            return false;
        }
        $now_date = date('Y-m-d');
        $filter = array('classroom_id'=>$room_id, 'member_id'=>$member_id);
        $student = $this->where($filter)->find();
        if (empty($student->id)) {
            return false;
        }
        elseif($now_date <= $student->end_date){
            return false;
        }
        else{
            return true;
        }
    }

    /* 添加学员
    *  必须参数 : member_id, classroom_id, realname, phone
    *  默认参数 : from_type = 'fee'
    *  判断学员是否存在 : member_id, classroom_id
    *  调用方式 : try 捕捉异常
    */
    public function addone($data)
    {
        if (empty($data['member_id'])) {
            throw new Exception("会员ID为空", 1001);
        }
        elseif (empty($data['classroom_id'])) {
            throw new Exception("课程ID为空", 1002);
        }
        elseif (empty($data['realname'])) {
            throw new Exception("真实姓名为空", 1003);
        }
        elseif (empty($data['phone'])) {
            throw new Exception("电话为空", 1004);
        }

        if (empty($data['from_type'])) {
            $data['from_type'] = 'fee';
        }
        $filter = array(
            'classroom_id'  => $data['classroom_id'],
            'member_id'     => $data['member_id']
        );
        $this->where($filter)->find();
        if (!empty($this->id)) {
            throw new Exception("该学员已加入该课程", 1005);
        }
        else {
            $this->add($data);
        }
        return $this;
    }



    public function get_zhifubao_room($room_id, $member_id)
    {
        $filter = array('classroom_id'=> $room_id, 'member_id'=>$member_id);
        $now = $this->where($filter)->find();

        $now_id = $now->id;
        $zhifubao = $now->zhifubao;

        if ( !empty($now_id) and empty($zhifubao) ) {
            $zhifubao = $this->get_zhifubao_all($member_id);
            if (!empty($zhifubao)) {
                $this->find($now_id);
                $this->zhifubao = $zhifubao;
                $this->save();
            }
        }
        return $zhifubao;
    }

    protected function get_zhifubao_all($member_id)
    {
        $filter = array('member_id'=>$member_id);
        $students = $this->where($filter)->find_all();
        foreach ($students as $key => $student) {
            if (!empty($student->zhifubao)) {
                return $student->zhifubao;
            }
        }
        return false;
    }

    // 会员数
    public function count($room_id)
    {
        $filter = array('classroom_id'=>$room_id, 'from_type'=>'fee');
        return $this->where($filter)->count();

    }

}
