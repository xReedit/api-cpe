<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnulmentDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'annulment_id',
        'document_id',
        'description'
    ];

    public function annulment()
    {
        return $this->belongsTo(Annulment::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}