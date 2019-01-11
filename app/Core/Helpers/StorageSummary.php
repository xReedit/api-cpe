<?php

namespace App\Core\Helpers;

use App\Models\Summary;
use Illuminate\Support\Facades\Storage;

class StorageSummary
{
    protected $summary;
    protected $client;
    protected $path;
    protected $disk;

    public function summaryById($id)
    {
        $this->summary = Summary::with(['user'])->find($id);
        $this->setClient();
        $this->setPath();
    }

    private function setClient()
    {
        $this->client = $this->summary->user->client;
    }

    private function setPath()
    {
        $this->disk = env('STORAGE_DRIVER');
        $this->path = $this->client->company_number.DIRECTORY_SEPARATOR.(($this->summary->soap_type_id === '01')?'demo':'production');
        if (!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.$this->path)) && ($this->disk === 'local')) {
            Storage::makeDirectory($this->path);
        }
    }

    public function uploadXml($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.$this->summary->filename.'.xml', $content);
    }

    public function uploadCdr($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.'R-'.$this->summary->filename.'.xml', $content);
    }

    public function downloadXml()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.$this->summary->filename.'.xml');
    }

    public function downloadCdr()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.'R-'.$this->summary->filename.'.xml');
    }
    
    public function getXml()
    {
        return Storage::disk($this->disk)->get($this->path.DIRECTORY_SEPARATOR.$this->summary->filename.'.xml');
    }
}