<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    /**
     * 获得与工作关联的城市
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id' ,'id');
    }

    /**
     * 获得与工作关联的公司
     */
    public function company()
    {
        return $this->hasOne('App\Models\CompanyMember','member_id','member_id');
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

        $res = $list->orderBy($order, $by)->with('city', 'company')->paginate($pageSize);

        foreach($res as $k=>$v){
            $res[$k]->cityinfo = $this->find($v->id)->city;
            $res[$k]->companyinfo = $this->find($v->id)->company;
        }

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
            if (isset($data['member_id']) && $data['member_id']) {
                $filter['member_id'] = $data['member_id'];
            }
            if (isset($data['province_id']) && $data['province_id']) {
                $filter['province_id'] = $data['province_id'];
            }
            if (isset($data['city_id']) && $data['city_id']) {
                $filter['city_id'] = $data['city_id'];
            }
            if (isset($data['n_views']) && $data['n_views']) {
                $filter['n_views'] = $data['n_views'];
            }
            if (isset($data['type_id']) && $data['type_id']) {
                $filter['type_id'] = $data['type_id'];
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
        $res->cityinfo = $this->find($res->id)->city;
        $res->companyinfo = $this->find($res->id)->company;
        return $res;
    }




}
