<?php
/**
 * Created by PhpStorm.
 * User: gaoxing
 * Date: 17/11/21
 * Time: 下午6:40
 */
namespace App\Models;

class M3Result {

    public $status;
    public $message;

    public function toJson()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

}