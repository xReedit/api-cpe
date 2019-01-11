<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    static function idByDescription($description)
    {
        $code = static::where('description', $description)->get();
        if (count($code) > 0) {
            return $code[0]->id;
        }
        return '150101';
    }
}