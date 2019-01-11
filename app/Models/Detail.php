<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'item_description',
        'unit_type_code',
        'quantity',
        'unit_value',
        'price_type_code',
        'unit_price',
        'affectation_igv_type_code',
        'total_igv',
        'percentage_igv',
        'system_isc_type_code',
        'total_isc',
        'charge_type_code',
        'charge_percentage',
        'total_charge',
        'discount_type_code',
        'discount_percentage',
        'total_discount',
        'subtotal',
        'total',
        'additional'
    ];

    protected $casts = [
        'date_of_document' => 'date',
    ];

    public function getAdditionalAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setAdditionalAttribute($value)
    {
        $this->attributes['additional'] = json_encode($value);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}