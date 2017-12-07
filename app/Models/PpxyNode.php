<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyNode extends Model
{
   	protected $table = 'ppxy_nodes';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;

    /*
    * index
    * */
    public function index($data)
    {
        //过滤查询条件
        $pageSize = 10;
        $page = 1;
        $filter = $this->zf_search($data);
        $select = "*";
        $order = 'n_weight';
        $by = 'asc';
        if (isset($data['per_page']) && $data['per_page']) {
            $pageSize = $data['per_page'];
        }
        if (isset($data['page']) && $data['page']) {
            $page = $data['page'];
        }
        if (isset($data['order']) && $data['order'] && isset($data['by']) && $data['by']) {
            $order = $data['orderby'];
            $by = $data['by'];
        }
        if (isset($data['select']) && $data['select']) {
            $select = explode(',', $data['select']);
        }
        $list = $this->where($filter)->select($select);

        $res = $list->orderBy($order, $by)->paginate($pageSize);

        return $res;
    }

    /*
     * 组装查询条件
     * */
    public function zf_search($data)
    {
        $filter = array();
        if (is_array($data) && $data) {
            if (isset($data['member_id']) && $data['member_id']) {
                $filter['member_id'] = $data['member_id'];
            }
            if (isset($data['classroom_id']) && $data['classroom_id']) {
                $filter['classroom_id'] = $data['classroom_id'];
            }
            if (isset($data['poly_code']) && $data['poly_code']) {
                $filter['poly_code'] = $data['poly_code'];
            }
            if (isset($data['v_type']) && $data['v_type']) {
                $filter['v_type'] = $data['v_type'];
            }
            if (isset($data['if_answered']) && $data['if_answered']) {
                $filter['if_answered'] = $data['if_answered'];
            }
            if (isset($data['if_ag']) && $data['if_ag']) {
                $filter['if_ag'] = $data['if_ag'];
            }
            if (isset($data['if_qa']) && $data['if_qa']) {
                $filter['if_qa'] = $data['if_qa'];
            }
            if (isset($data['n_views']) && $data['n_views']) {
                $filter['n_views'] = $data['n_views'];
            }
            if (isset($data['menu_id']) && $data['menu_id']) {
                $filter['menu_id'] = $data['menu_id'];
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


