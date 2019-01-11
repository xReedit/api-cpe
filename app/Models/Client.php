<?php

namespace App\Models;

use App\Models\System\SoapType;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'company_name',
        'company_number',
        'soap_type_id',
        'soap_username',
        'soap_password',
        'certificate'
    ];
}
