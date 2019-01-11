<?php

namespace App\Core\Helpers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class StorageDocument
{
    protected $document;
    protected $client;
    protected $path;
    protected $disk;

    public function documentById($id)
    {
        $this->document = Document::with(['user'])->find($id);
        $this->setClient();
        $this->setPath();
    }

    public function documentByExternalId($external_id)
    {
        $this->document = Document::with(['user'])->where('external_id', $external_id)->first();
        $this->setClient();
        $this->setPath();
    }

    private function setClient()
    {
        $this->client = $this->document->user->client;
    }

    private function setPath()
    {
        $this->disk = env('STORAGE_DRIVER');
        $this->path = $this->client->company_number.DIRECTORY_SEPARATOR.(($this->document->soap_type_id === '01')?'demo':'production');
        if (!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.$this->path)) && ($this->disk === 'local')) {
            Storage::makeDirectory($this->path);
        }
    }

    public function uploadXml($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.xml', $content);
    }

    public function uploadCdr($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.'R-'.$this->document->filename.'.xml', $content);
    }

    public function uploadPdf($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.pdf', $content);
    }

    public function downloadPdf()
    {
        // return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.pdf');
        
        // abre sin descargar
        return Storage::response($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.pdf');
    }

    public function downloadXml()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.xml');
    }

    public function downloadCdr()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.'R-'.$this->document->filename.'.xml');
    }
    
    public function getXml()
    {
        return Storage::disk($this->disk)->get($this->path.DIRECTORY_SEPARATOR.$this->document->filename.'.xml');
    }
}