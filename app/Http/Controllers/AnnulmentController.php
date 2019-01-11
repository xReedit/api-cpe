<?php
namespace App\Http\Controllers;

use App\Core\Builder\CpeAnnulmentBuilder;
use App\Core\Builder\Documents\AnnulmentBuilder;
use App\Core\Builder\XmlAnnulmentBuilder;
use App\Core\Helpers\StorageAnnulment;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\AnnulmentCollection;
use App\Models\Client;
use App\Models\Document;
use App\Models\Annulment;
use Illuminate\Http\Request;

class AnnulmentController extends Controller
{
    public function index()
    {
        return view('annulments.index');
    }

    public function records()
    {
        $records = Annulment::with(['state_type'])->advancedFilter();
        return new AnnulmentCollection($records);
    }

    public function tables()
    {
        $clients = Client::all();
        return to_json(compact('clients'));
    }

    public function searchDocuments(Request $request)
    {
        $documents = Document::where('date_of_issue', $request->input('date_of_reference'))
                              ->where('state_type_id', '02')
                              ->where('document_type_code', '01')
                              ->where('user_id', $request->input('user_id'))
                              ->get();
        return new DocumentCollection($documents);
    }

    public function store(Request $request)
    {
        $builder = new AnnulmentBuilder();
        $builder->save($request->all());

        $xmlBuilder = new XmlAnnulmentBuilder();
        $res = $xmlBuilder->createXMLSigned($builder);

        if($res['success']) {
            $storageAnnulment = new StorageAnnulment();
            $storageAnnulment->annulmentById($res['annulment_id']);
            $storageAnnulment->uploadXml($res['xml']);

            $cpeBuilder = new CpeAnnulmentBuilder($res['annulment_id']);
            $res_cpe = $cpeBuilder->sendXml();

            $annulment = Annulment::find($res['annulment_id']);
            if($res_cpe['success']) {
                $annulment->state_type_id = 'a1';
                $annulment->ticket = $res_cpe['ticket'];
                $annulment->save();
                return to_json([
                    'success' => true,
                    'message' => __('app.actions.annulment.success')
                ]);
            } else {
                $annulment->state_type_id = 'a2';
                $annulment->save();
                return to_json_error($res_cpe['message'], 500);
            }
        } else {
            return to_json_error($res['message'], 500);
        }
    }

    public function checkTicket($id)
    {
        $cpeBuilder = new CpeAnnulmentBuilder($id);
        $res_cpe = $cpeBuilder->checkTicket();

        if ($res_cpe['success']) {
            $annulment = Annulment::find($id);
            $annulment->state_type_id = 'a3';
            $annulment->save();
            foreach ($annulment->documents as $doc)
            {
                $document = Document::find($doc->document_id);
                $document->state_type_id = '05';
                $document->save();
            }
        }

        return to_json([
            'success' => $res_cpe['success'],
            'message' => $res_cpe['message']
        ]);
    }

    public function download($type, $id)
    {
        $storageHelper = new StorageAnnulment();
        $storageHelper->annulmentById($id);
        if ($type === 'xml') {
            return $storageHelper->downloadXml();
        }
        if ($type === 'cdr') {
            return $storageHelper->downloadCdr();
        }
        return false;
    }
}