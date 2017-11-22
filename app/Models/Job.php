<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id' ;
    public $timestamps = false;
    /*
    *
    *职业机会
    **/
    public function getJobList($ids){
    	$data=array();
    	foreach ($ids as $key => $id) {
            $job =$this->find($id);
        
            $url = 'job/one/'.$id;
            $company = \App\Models\CompanyMember::where(array('member_id'=>$job->member_id))->first();
            if( !empty($job->pay_min) )
            {
                $salary = '月薪：￥'.$job->pay_min.',000 - ￥'.$job->pay_max.',000';
            }
            $city = \App\Models\City::find($job->city_id);
            $data[$key]['id']=$id;
            $data[$key]['url']=$url;
            $data[$key]['position']=$job->v_zhiwei;
            $data[$key]['city']=$city->v_name;
            $data[$key]['salary']=$salary;
            $data[$key]['company']=$company->company_name;
        }
        return $data;
    }


}
