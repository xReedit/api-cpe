<?php
namespace App\Http\Controllers\Api;

use App\Core\Builder\CpeBuilder;
use App\Core\Builder\SendBuilder;
use App\Core\Builder\XmlBuilder;
use App\Core\Helpers\StorageDocument;
use App\Core\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentControllerOld extends Controller
{
    public function signedXml(DocumentRequest $request)
    {
        $document = Document::create([
            'user_id' => auth()->user()->id,
            'external_id' => '',
            'state_type_id' => '01',
            'soap_type_id' => $request->input('document.soap_type_id'),
            'ubl_version' => $request->input('document.ubl_version'),
            'document_type_code' => $request->input('document.document_type_code'),
            'date_of_issue' => $request->input('document.date_of_issue'),
            'time_of_issue' => $request->input('document.time_of_issue'),
            'series' => $request->input('document.series'),
            'number' => $request->input('document.number'),
            'currency_type_code' => $request->input('document.currency_type_code'),
            'total_exportation' => $request->input('document.total_exportation'),
            'total_taxed' => $request->input('document.total_taxed'),
            'total_unaffected' => $request->input('document.total_unaffected'),
            'total_exonerated' => $request->input('document.total_exonerated'),
            'total_igv' => $request->input('document.total_igv'),
            'total_isc' => ($request->input('document.total_isc')) ?: 0,
            'total_other_taxes' => ($request->input('document.total_other_taxes')) ?: 0,
            'total_other_charges' => ($request->input('document.total_other_charges')) ?: 0,
            'total_discount' => ($request->input('document.total_discount')) ?: 0,
            'total_value_sale' => ($request->input('document.total_value_sale')) ?: 0,
            'total_price_sale' => ($request->input('document.total_price_sale')) ?: 0,
            'subtotal' => $request->input('document.subtotal'),
            'total' => $request->input('document.total'),
            'company' => json_encode($request->input('document.company')),
            'establishment' => json_encode($request->input('document.establishment')),
            'customer' => json_encode($request->input('document.customer')),
            'guide' => json_encode($request->input('document.guide')),
            'additional_documents' => json_encode($request->input('document.additional_documents')),
            'notes' => json_encode($request->input('document.notes')),
            'filename' => '',
            'hash' => '',
            'qr' => ''
        ]);

        foreach ($request->input('document.details') as $row) {
            $document->details()->create([
                'item_id' => $row['item_id'],
                'item_description' => $row['item_description'],
                'unit_type_code' => $row['unit_type_code'],
                'quantity' => $row['quantity'],
                'unit_value' => $row['unit_value'],
                'price_type_code' => $row['price_type_code'],
                'unit_price' => $row['unit_price'],
                'affectation_igv_type_code' => $row['affectation_igv_type_code'],
                'total_igv' => $row['total_igv'],
                'system_isc_type_code' => array_key_exists('system_isc_type_code', $row) ? $row['system_isc_type_code'] : null,
                'total_isc' => $row['total_isc'],
                'charge_or_discount_type_code' => array_key_exists('charge_or_discount_type_code', $row) ? $row['charge_or_discount_type_code'] : null,
                'charge_or_discount_percentage' => array_key_exists('charge_or_discount_percentage', $row) ? $row['charge_or_discount_percentage'] : 0,
                'total_charge_or_discount' => array_key_exists('total_charge_or_discount', $row) ? $row['total_charge_or_discount'] : 0,
                'subtotal' => $row['subtotal'],
                'total' => $row['total']
            ]);
        }

        $document->invoice()->create([
            'operation_type_code' => $request->input('invoice.operation_type_code'),
            'date_of_due' => $request->input('invoice.date_of_due'),
            'total_global_discount' => $request->input('invoice.total_global_discount'),
            'total_free' => $request->input('invoice.total_free'),
            'total_prepayment' => $request->input('invoice.total_prepayment'),
            'purchase_order' => $request->input('invoice.purchase_order'),
            'optional' => json_encode($request->input('invoice.optional')),
            'detraction' => json_encode($request->input('invoice.detraction')),
            'prepayments' => json_encode($request->input('invoice.prepayments')),
        ]);

        $document->logs()->create(['message' => __('app.logs.document.create')]);

        $data = (new XmlBuilder(Document::with(['details', 'invoice'])->find($document->id)))->createXMLSigned();
        $document->hash = $data['hash'];
        $document->qr = $data['qr'];
        $document->external_id = Str::uuid();
        $document->save();

        $document->logs()->create(['message' => __('app.logs.document.xml')]);

        $storageDocument = new StorageDocument();
        $storageDocument->documentById($document->id);
        $storageDocument->uploadXml($data['xml']);

        $document->logs()->create(['message' => __('app.logs.document.upload_xml')]);

        return to_json([
            'success' => true,
            'data' => [
                'hash' => $data['hash'],
                'qr' => $data['qr'],
                'filename' => $document->filename,
                'external_id' => $document->external_id
            ]
        ]);
    }

//    private function upload($document_id, $data)
//    {
//        $storageHelper = new StorageHelper();
//        $storageHelper->documentById($document_id);
//        $storageHelper->uploadXml($data['xml']);
//        $storageHelper->uploadPdf($this->createPdf($document_id));
//    }



//    public function createPdf($document_id)
//    {
//        $document = Document::with(['details', 'invoice'])->find($document_id);
//        $pdf = PDF::loadView('templates.pdf.default', compact('document') );
//        return $pdf->output();
//    }


    public function sendXml(Request $request)
    {
        $external_id = $request->input('external_id');
        $cpeBuilder = new CpeBuilder($external_id);
        $res = $cpeBuilder->sendXml();
        return to_json($res);
    }

    public function checkCdr(Request $request)
    {
        $external_id = $request->input('external_id');
        $cpeBuilder = new CpeBuilder($external_id);
        $res = $cpeBuilder->checkCdrStatus();
        return to_json($res);
    }

//    public function cdrStatus(Request $request)
//    {
//        $external_id = $request->input('external_id');
//        $extService = new SendBuilder($external_id);
//        $res = $extService->checkCdrStatus();
//        return to_json($res);
//    }
}