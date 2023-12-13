<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacModel extends Model
{
    protected $table = 'cac_leads';
    //public $timestamps = false;
    protected $fillable = [
        'form_name',
        'platform',
        'state',
        'city',
        'first_name',
        'last_name',
        'company_name',
        'phone',
        'email',
        'remark'
    ];
}
