<?php
namespace App\Http\Controllers;

use App\Core\Builder\CpeSummaryBuilder;
use App\Core\Builder\Documents\SummaryBuilder;
use App\Core\Builder\XmlSummaryBuilder;
use App\Core\Helpers\StorageSummary;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\SummaryCollection;
use App\Models\Client;
use App\Models\Document;
use App\Models\Summary;
use Illuminate\Http\Request;

class SummaryAnnulmentController extends Controller
{
    public function index()
    {
        return view('summaries_annulment.index');
    }

    public function records()
    {
        $records = Summary::with(['state_type'])->where('type', 3)->advancedFilter();
        return new SummaryCollection($records);
    }

    public function tables()
    {
        $clients = Client::all();
        return to_json(compact('clients'));
    }

    public function searchDocuments(Request $request)
    {
        $documents = Document::where('date_of_issue', $request->input('date_of_reference'))
                              ->where('document_type_code', '03')
                              ->where('user_id', $request->input('user_id'))
                              ->get();
        return new DocumentCollection($documents);
    }

    public function store(Request $request)
    {
        $builder = new SummaryBuilder();
        $builder->save($request->all());

        $xmlBuilder = new XmlSummaryBuilder();
        $res = $xmlBuilder->createXMLSigned($builder);

        if($res['success']) {
            $storageSummary = new StorageSummary();
            $storageSummary->summaryById($res['summary_id']);
            $storageSummary->uploadXml($res['xml']);

            $cpeBuilder = new CpeSummaryBuilder($res['summary_id']);
            $res_cpe = $cpeBuilder->sendXml();

            $summary = Summary::find($res['summary_id']);
            if($res_cpe['success']) {
                $summary->state_type_id = 'a1';
                $summary->ticket = $res_cpe['ticket'];
                $summary->save();
                return to_json([
                    'success' => true,
                    'message' => __('app.actions.summary.success')
                ]);
            } else {
                $summary->state_type_id = 'a2';
                $summary->save();
                return to_json_error($res_cpe['message'], 500);
            }
        } else {
            return to_json_error($res['message'], 500);
        }
    }

    public function checkTicket($id)
    {
        $cpeBuilder = new CpeSummaryBuilder($id);
        $res_cpe = $cpeBuilder->checkTicket();

        if ($res_cpe['success']) {
            $summary = Summary::find($id);
            $summary->state_type_id = 'a3';
            $summary->save();
            foreach ($summary->documents as $doc)
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
}