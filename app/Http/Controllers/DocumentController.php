<?php
namespace App\Http\Controllers;

use App\Core\Helpers\StorageDocument;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\DocumentCollection;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        return view('documents.index');
    }

    public function records()
    {
        $records = Document::with(['soap_type', 'state_type'])->advancedFilter();
        return new DocumentCollection($records);
    }

    public function download($type, $external_id)
    {
        $storageHelper = new StorageDocument();
        $storageHelper->documentByExternalId($external_id);
        if ($type === 'pdf') {
            return $storageHelper->downloadPdf();
        }
        if ($type === 'xml') {
            return $storageHelper->downloadXml();
        }
        if ($type === 'cdr') {
            return $storageHelper->downloadCdr();
        }
        return false;
    }
}