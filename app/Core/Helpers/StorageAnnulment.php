<?php

namespace App\Core\Helpers;

use App\Models\Annulment;
use Illuminate\Support\Facades\Storage;

class StorageAnnulment
{
    protected $annulment;
    protected $client;
    protected $path;
    protected $disk;

    public function annulmentById($id)
    {
        $this->annulment = Annulment::with(['user'])->find($id);
        $this->setClient();
        $this->setPath();
    }

    private function setClient()
    {
        $this->client = $this->annulment->user->client;
    }

    private function setPath()
    {
        $this->disk = env('STORAGE_DRIVER');
        $this->path = $this->client->company_number.DIRECTORY_SEPARATOR.(($this->annulment->soap_type_id === '01')?'demo':'production');
        if (!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.$this->path)) && ($this->disk === 'local')) {
            Storage::makeDirectory($this->path);
        }
    }

    public function uploadXml($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.$this->annulment->filename.'.xml', $content);
    }

    public function uploadCdr($content)
    {
        Storage::disk($this->disk)->put($this->path.DIRECTORY_SEPARATOR.'R-'.$this->annulment->filename.'.xml', $content);
    }

    public function downloadXml()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.$this->annulment->filename.'.xml');
    }

    public function downloadCdr()
    {
        return Storage::disk($this->disk)->download($this->path.DIRECTORY_SEPARATOR.'R-'.$this->annulment->filename.'.xml');
    }
    
    public function getXml()
    {
        return Storage::disk($this->disk)->get($this->path.DIRECTORY_SEPARATOR.$this->annulment->filename.'.xml');
    }
}