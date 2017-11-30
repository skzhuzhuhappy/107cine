<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class GeneralType extends Model
{
  protected  $table = 'general_types';
  protected  $primaryKey = 'id' ;
  public  $timestamps = false;

  public static function type_num(){

    $sql = ' SELECT gt.id id, gt.name_cn name, mm.cum cum FROM `general_types` gt LEFT JOIN
    (SELECT COUNT(*) cum, main_id FROM `ppxy_classrooms` where if_course="Y" GROUP BY `main_id`) mm on mm.main_id = gt.id WHERE gt.`parent_id` = 1 order by gt.n_weight asc';
    //$data = new Database;
    //$mains = $data->query($sql);
    $mains= DB::select($sql);
    $pics = array('sort_bg5.jpg', 'sort_bg6.jpg', 'sort_bg8.jpg', 'sort_bg7.jpg');
    $i = 0;
    foreach ($mains as $key => $main) {
        $main->cum = empty($main->cum) ? 0 : $main->cum;
        $main->pic = 'front/images/ppxy2/index/'.$pics[$i];
        $i++;
    }
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
        $return = array('id'=>$this->id, 'name'=>$this->name_cn);
        if ($this->count > 0) {
             $values = $this->where(array('parent_id'=>$id))->orderby(array('n_weight'=>'asc'))->get();
            foreach ($values as $key => $value) {
                $return['value'][] = $this->lists($value->id);
            }           
        }
        return $return;
    }

    public function dropdown($id)
    {
        $values = $this->where(array('parent_id'=>$id))->orderby(array('n_weight'=>'asc'))->find_all();
        foreach ($values as $key => $value) {
            $return[$value->id] = $value->name_cn;
        }        
        return $return;           
    }

    //栏目导航
    public function course_nav($sub_id,$main_id){
        
        $types = $this->lists(1);
        $mains = $types['value'];
        
        if ($this->if_mobile == false) {
            $data=array();
            foreach ($mains as $key => $main) {

                $data[$key]['hd']=$main['name'];
                $subs = $main['value'];
                $sel = ($main['id'] == $main_id and empty($sub_id)) ? 'sel' : '';
              
                $data[$key]['href']="ppxy/course?main_id=".$main['id'];
                $data[$key]['class']=$sel;
                $sub_data=array();
                foreach ($subs as $key => $sub) {
                    $sel = ($sub['id'] == $sub_id) ? 'sel' : '';
                    $sub_data[$key]['href']="ppxy/course?main_id=".$main['id']."&sub_id=".$sub['id'];
                    $sub_data[$key]['class']=$sel;
                    $sub_data[$key]['value']=$sub['name'];
                }
                $data[$key]['subs']=$sub_data;
               
            }
            return $data;
        }
    }
        

}
