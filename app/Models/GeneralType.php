<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class GeneralType extends Model
{
    protected $table = 'general_types';
    protected $primaryKey = 'id';
    public $timestamps = false;


    /**
     * 获得课程数量
     */
    public function classroom_main()
    {
        return $this->hasMany('App\Models\Classroom', 'main_id', 'id');
    }

    /**
     * 获得课程数量
     */
    public function classroom_sub()
    {
        return $this->hasMany('App\Models\Classroom', 'sub_id', 'id');
    }

    /**
     * 获得课程数量
     */
    public function genera()
    {
        return $this->hasMany('App\Models\GeneralType', 'parent_id', 'id');
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

        $res = $this->where($filter)->orderBy($order, $by)->withcount('classroom_main', 'classroom_sub')->paginate($pageSize);


        return $res;
    }

    /*
     * 组装查询条件
     * */
    public function zf_search($data)
    {
        $filter = array();
        if (is_array($data) && $data) {
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


    /*
     * 组装数据
     * */
    public function get_list($data)
    {
        $filter = $this->zf_search($data);
        return $this->where($filter)->with('genera')->get();
    }


    /*
     * 获得类型 数量
     * */
    public static function type_num()
    {

        $sql = ' SELECT gt.id id, gt.name_cn name, mm.cum cum FROM `general_types` gt LEFT JOIN
    (SELECT COUNT(*) cum, main_id FROM `ppxy_classrooms` where if_course="Y" GROUP BY `main_id`) mm on mm.main_id = gt.id WHERE gt.`parent_id` = 1 order by gt.n_weight asc';
        $mains = DB::select($sql);
        return $mains;

    }

    public function add($data)
    {
        $id = parent::add($data);
        if (!empty($data['parent_id'])) {
            $this->find($data['parent_id']);
            $this->count = $this->count + 1;
            $this->save();
        }
        return $id;
    }

    public function lists($id)
    {
        $id = intval($id);
        if (empty($id)) {
            exit();
        }
        $this->find($id);
        $return = array('id' => $this->id, 'name' => $this->name_cn);
        if ($this->count > 0) {
            $values = $this->where(array('parent_id' => $id))->orderby(array('n_weight' => 'asc'))->get();
            foreach ($values as $key => $value) {
                $return['value'][] = $this->lists($value->id);
            }
        }
        return $return;
    }

    public function dropdown($id)
    {
        $values = $this->where(array('parent_id' => $id))->orderby(array('n_weight' => 'asc'))->find_all();
        foreach ($values as $key => $value) {
            $return[$value->id] = $value->name_cn;
        }
        return $return;
    }

    //栏目导航
    public function course_nav($sub_id, $main_id)
    {

        $types = $this->lists(1);
        $mains = $types['value'];

        if ($this->if_mobile == false) {
            $data = array();
            foreach ($mains as $key => $main) {

                $data[$key]['hd'] = $main['name'];
                $subs = $main['value'];
                $sel = ($main['id'] == $main_id and empty($sub_id)) ? 'sel' : '';

                $data[$key]['href'] = "ppxy/course?main_id=" . $main['id'];
                $data[$key]['class'] = $sel;
                $sub_data = array();
                foreach ($subs as $key => $sub) {
                    $sel = ($sub['id'] == $sub_id) ? 'sel' : '';
                    $sub_data[$key]['href'] = "ppxy/course?main_id=" . $main['id'] . "&sub_id=" . $sub['id'];
                    $sub_data[$key]['class'] = $sel;
                    $sub_data[$key]['value'] = $sub['name'];
                }
                $data[$key]['subs'] = $sub_data;

            }
            return $data;
        }
    }


    /*
    * 获得 menu 列表
    * */
    function CateTree($pid = 0, $level = 0)
    {
        $array = array();
        $tmp = $this->where(['parent_id' => $pid])->orderBy("n_weight", "asc")->get()->toArray();
        if (is_array($tmp)) {
            foreach ($tmp as $v) {
                $v['level'] = $level;
                //$v['pid']>0;
                $array[count($array)] = $v;
                $sub = $this->CateTree($v['id'], $level + 1);
                if (is_array($sub)) $array = array_merge($array, $sub);
            }
        }
        return $array;
    }


}
