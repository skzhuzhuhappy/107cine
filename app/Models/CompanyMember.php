<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMember extends Model
{
  protected $table = 'company_members';
  protected $primaryKey = 'id' ;
  public $timestamps = false;

}
