<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Common extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $request->status=200;
        $request->msg='请求成功';
        return parent::toArray($request);
    }
}
