<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyBaseMenu extends Model
{
    protected $table = 'ppxy_base_menus';
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
            if (isset($data['room_id']) && $data['room_id']) {
                $filter['room_id'] = $data['room_id'];
            }
            if (isset($data['parent_id']) && $data['parent_id']) {
                $filter['parent_id'] = $data['parent_id'];
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

