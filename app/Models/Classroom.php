<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'ppxy_classrooms';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $product_ids = array();
    protected $ym, $next_ym;
    protected $student_count;

    /**
     * 获得学生 信息
     */
    public function student()
    {
        return $this->hasMany('App\Models\PpxyStudent', 'classroom_id' ,'id');
    }


    /*
     * index
     * */
    public function index($data)
    {

        //过滤查询条件
        $pageSize = 10;
        $page = 1;
        $filter = $this->zf_search($data);
        $select = "id,type,v_title,teacher_name,teacher_desc,n_weight,d_time,v_pic,pre_pic,product_id,is_del,spe_name,start_date,main_id,sub_id,if_online,if_star,if_free,if_home,if_course,if_train";
        $order = 'n_weight';
        $by = 'asc';
        if (isset($data['per_page']) && $data['per_page']) {
            $pageSize = $data['per_page'];
        }
        if (isset($data['page']) && $data['page']) {
            $page = $data['page'];
        }
        if (isset($data['order']) && $data['order'] && isset($data['by']) && $data['by']) {
            $order = $data['order'];
            $by = $data['by'];
        }
        if (isset($data['select']) && $data['select']) {
            $select=$data['select'];
        }
        $select = explode(',', $select);
        $class_room_list = $this->where($filter)->select($select);

        if(isset($data['member_id'])&&$data['member_id']){
            $classroom_id_arr = \App\Models\PpxyStudent::where(array('member_id' => $data['member_id']))->pluck('classroom_id')->toArray();
            $class_room_list->whereIn('id', $classroom_id_arr);
        }

        $res=$class_room_list->orderBy($order, $by)->withCount('student')->paginate($pageSize);

        return $res;
    }

    /*
     * 组装查询条件
     * */
    public function zf_search($data)
    {
        $filter = array();
        $filter['is_del'] = "N";
        if (is_array($data) && $data) {
            if (isset($data['type']) && $data['type']) {
                $filter['type'] = $data['type'];
            }
            if (isset($data['product_id']) && $data['product_id']) {
                $filter['product_id'] = $data['product_id'];
            }
            if (isset($data['is_del']) && $data['is_del']) {
                $filter['is_del'] = $data['is_del'];
            }
            if (isset($data['main_id']) && $data['main_id']) {
                $filter['main_id'] = $data['main_id'];
            }
            if (isset($data['sub_id']) && $data['sub_id']) {
                $filter['sub_id'] = $data['sub_id'];
            }
            if (isset($data['if_online']) && $data['if_online']) {
                $filter['if_online'] = $data['if_online'];
            }
            if (isset($data['if_star']) && $data['if_star']) {
                $filter['if_star'] = $data['if_star'];
            }
            if (isset($data['if_free']) && $data['if_free']) {
                $filter['if_free'] = $data['if_free'];
            }
            if (isset($data['if_home']) && $data['if_home']) {
                $filter['if_home'] = $data['if_home'];
            }
            if (isset($data['if_course']) && $data['if_course']) {
                $filter['if_course'] = $data['if_course'];
            }
            if (isset($data['if_train']) && $data['if_train']) {
                $filter['if_train'] = $data['if_train'];
            }

        }
        return $filter;
    }

    /*
     * 获得一条
     * */
    public function show($id)
    {
        $res = $this->find($id);
        return $res;
    }

}
