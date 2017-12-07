<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
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
        $order = 'id';
        $by = 'desc';
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
        $res=$list->orderBy($order, $by)->paginate($pageSize);

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
            if (isset($data['province_id']) && $data['province_id']) {
                $filter['province_id'] = $data['province_id'];
            }
            if (isset($data['code']) && $data['code']) {
                $filter['code'] = $data['code'];
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
