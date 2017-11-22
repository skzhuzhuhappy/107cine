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

}
