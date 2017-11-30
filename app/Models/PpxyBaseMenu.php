<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpxyBaseMenu extends Model
{
    protected $table = 'ppxy_base_menus';
  	protected $primaryKey = 'id' ;
  	public $timestamps = false;

    //课程大纲
    public function base_menu_room_id($room_id){
        $show_desc = array(47, 25, 51);
        $menus = \App\Models\PpxyBaseMenu::where(array('room_id'=>$room_id))->orderby(array('n_weight'=>'asc'))->get();
        $count = count($menus);
        $i = 1;
        $more_text = '显示全部课程大纲';
        $data = array();

        foreach ($menus as $key => $menu) {

            if ($i >= $count) {
                break;
            }
            /*if ($i == 4) {
                echo '<div class="more more_h"><i class="icon-control-arr-copy-copy"></i><span>'.$more_text.'</span>';
            }*/

            $data[$key]['number'] = $i;
            $data[$key]['v_name'] = $menu->v_name;
            /*if ( in_array($room_id, $show_desc) ) {
                echo html::simple_show_content($menu->t_des);
                $data[$key]['v_name'] = $menu->t_des;
            }
            else {
                $this->item($menu, $room_id);
            }*/


            $i++;
        }


        return $data;


        /*if ($i > 4) {
            if ($room->id == 25) {
                echo '<div class="item"> 持续更新中…… </div>';
            }
            echo '</div>';
        }*/


        /*echo '<div class="item no-border"><span class="num"><span class="num_icon"></span>'.$i.'</span><dl><dt>'.$menu->v_name.'</dt><dd>';
        item($menu, $room);
        echo '</dd></dl></div>';*/
    }


    /*
     * 查看全部课程大纲
     * */
    public function menu_info_room_id(){
        $show_desc = array(47, 25, 51);
        $menus = \App\Models\PpxyBaseMenu::where(array('room_id'=>$room_id))->orderby(array('n_weight'=>'asc'))->get();
        $count = count($menus);
        $i = 1;
        $more_text = '显示全部课程大纲';
        $data = array();

        foreach ($menus as $key => $menu) {

            if ($i >= $count) {
                break;
            }
            /*if ($i == 4) {
                echo '<div class="more more_h"><i class="icon-control-arr-copy-copy"></i><span>'.$more_text.'</span>';
            }*/

            $data[$key]['number'] = $i;
            $data[$key]['v_name'] = $menu->v_name;
            /*if ( in_array($room_id, $show_desc) ) {
                echo html::simple_show_content($menu->t_des);
                $data[$key]['v_name'] = $menu->t_des;
            }
            else {

            }*/

            $data[$key]['item'] = $this->item($menu, $room_id);
            $i++;
        }
        return $data;
    }





    function item($menu, $room_id)
    {
        $nodes = \App\Models\PpxyNode::where(array('menu_id'=>$menu->id))->orderby(array('n_weight'=>'asc'))->find_all();
        $j = 1;
        if (count($nodes) > 0) {
            $data = array();
            foreach ($nodes as $key => $node) {
                $url = 'ppxy/node/'.$room_id.'/'.$node->id
                $data[$key]['href'] = $url;
                $data[$key]['value'] = $node->v_title;
                $j++;
            }
            return $data;
        }
        else {
            return $menu->t_des;
        }
    }


}

