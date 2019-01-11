<?php

namespace App\Models;

use App\Models\System\DataViewer;
use App\Models\System\SoapType;
use App\Models\System\StateType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use DataViewer;

    protected $fillable = [
        'user_id',
        'type',
        'state_type_id',
        'soap_type_id',
        'date_of_issue',
        'date_of_reference',
        'identifier',
        'filename',
        'ticket',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_reference' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function documents()
    {
        return $this->hasMany(SummaryDocument::class);
    }

    public function setFilenameAttribute($value)
    {
        $user = User::with(['client'])->find(request()->input('user_id'));
        $company_number = $user->client->company_number;
        $date_of_issue = Carbon::parse(request()->input('date_of_issue'));

        $summaries = Summary::select('id')->where('date_of_issue', $date_of_issue)
                                          ->where('user_id', $user->id)
                                          ->get();

        $numeration = count($summaries) + 1;
        $this->attributes['identifier'] = join('-', ['RC', $date_of_issue->format('Ymd'), $numeration]);
        $this->attributes['filename'] = $company_number.'-'.$this->attributes['identifier'];
    }

    public function getDownloadCdrAttribute()
    {
        return route('summaries.download', ['type' => 'cdr', 'id' => $this->id]);
    }

    public function getDownloadXmlAttribute()
    {
        return route('summaries.download', ['type' => 'xml', 'id' => $this->id]);
    }
}