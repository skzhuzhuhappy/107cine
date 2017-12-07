<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMember extends Model
{
    protected $table = 'company_members';
    protected $primaryKey = 'id';
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
            if (isset($data['is_approved']) && $data['is_approved']) {
                $filter['is_approved'] = $data['is_approved'];
            }
            if (isset($data['member_id']) && $data['member_id']) {
                $filter['member_id'] = $data['member_id'];
            }
            if (isset($data['company_type']) && $data['company_type']) {
                $filter['company_type'] = $data['company_type'];
            }
            if (isset($data['province_id']) && $data['province_id']) {
                $filter['province_id'] = $data['province_id'];
            }
            if (isset($data['city_id']) && $data['city_id']) {
                $filter['city_id'] = $data['city_id'];
            }
            if (isset($data['card_number']) && $data['card_number']) {
                $filter['card_number'] = $data['card_number'];
            }
            if (isset($data['subdomain']) && $data['subdomain']) {
                $filter['subdomain'] = $data['subdomain'];
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
