<?php

namespace App\Models;

use App\Models\System\DataViewer;
use App\Models\System\SoapType;
use App\Models\System\StateType;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use DataViewer;

    protected $fillable = [
        'user_id',
        'external_id',
        'state_type_id',
        'soap_type_id',
        'ubl_version',
        'document_type_code',
        'date_of_issue',
        'time_of_issue',
        'series',
        'number',
        'currency_type_code',
        'total_exportation',
        'total_taxed',
        'total_unaffected',
        'total_exonerated',
        'total_igv',
        'total_isc',
        'total_other_taxes',
        'total_other_charges',
        'total_discount',
        'total_value_sale',
        'total_price_sale',
        'subtotal',
        'total',
        'company',
        'establishment',
        'customer',
        'guides',
        'additional_documents',
        'legends',
        'optional',
        'filename',
        'hash',
        'qr'
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];

    public function getCompanyAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setCompanyAttribute($value)
    {
        $this->attributes['company'] = json_encode($value);
    }

    public function getEstablishmentAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = json_encode($value);
    }

    public function getCustomerAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = json_encode($value);
    }

    public function getGuidesAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setGuidesAttribute($value)
    {
        $this->attributes['guides'] = json_encode($value);
    }

    public function getAdditionalDocumentsAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setAdditionalDocumentsAttribute($value)
    {
        $this->attributes['additional_documents'] = json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = json_encode($value);
    }

    public function getOptionalAttribute($value)
    {
        return (object) json_decode($value);
    }

    public function setOptionalAttribute($value)
    {
        $this->attributes['optional'] = json_encode($value);
    }

    public function getNumberToLetterAttribute()
    {
        $legends = $this->legends;
        $legend = collect($legends)->where('code', '1000')->first();
        return $legend->description;
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function note()
    {
        return $this->hasOne(Note::class);
    }

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function setFilenameAttribute($value)
    {
        $company = request()->input('document.company');
        $document_type_code = request()->input('document.document_type_code');
        $series = request()->input('document.series');
        $number = request()->input('document.number');

        $this->attributes['filename'] = join('-', [$company['number'], $document_type_code, $series, $number]);
    }

    public function getDownloadPdfAttribute()
    {
        return route('documents.download', ['type' => 'pdf', 'external_id' => $this->external_id]);
    }

    public function getDownloadCdrAttribute()
    {
        return route('documents.download', ['type' => 'cdr', 'external_id' => $this->external_id]);
    }

    public function getDownloadXmlAttribute()
    {
        return route('documents.download', ['type' => 'xml', 'external_id' => $this->external_id]);
    }
}