<?php
namespace App\Http\Controllers\Api;

use App\Core\Builder\CpeBuilder;
use App\Core\Builder\Documents\InvoiceBuilder;
use App\Core\Builder\Documents\NoteCreditBuilder;
use App\Core\Builder\Documents\NoteDebitBuilder;
use App\Core\Builder\XmlBuilder;
use App\Core\Helpers\StorageDocument;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DocumentRequest;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('transform.input', ['only' => ['signedXml']]);
    }

    public function signedXml(DocumentRequest $request)
    {
        $document_type_code = $request->input('document.document_type_code');

        switch ($document_type_code) {
            case '01':
            case '03':
                $builder = new InvoiceBuilder();
                break;
            case '07':
                $builder = new NoteCreditBuilder();
                break;
            case '08':
                $builder = new NoteDebitBuilder();
                break;
            default:
                $builder = null;
        }

        $builder->save($request->all());

        $xmlBuilder = new XmlBuilder();
        $res = $xmlBuilder->createXMLSigned($builder);

        $storageDocument = new StorageDocument();
        $storageDocument->documentById($res['document_id']);
        $storageDocument->uploadXml($res['xml']);


        if ($res['success']) {
            $document = $builder->updateDocument($res['hash'], $res['qr'], Str::uuid());
            $storageDocument->uploadPdf($this->createPdf($builder));

            $res_cdr = $res;
//                $cpeBuilder = new CpeBuilder($document->external_id);
//                $res_cdr = $cpeBuilder->sendXml();

            if($res_cdr['success']) {
                return to_json([
                    'success' => true,
                    'data' => [
                        'hash' => $document->hash,
                        'qr' => $document->qr,
                        'filename' => $document->filename,
                        'external_id' => $document->external_id,
                        'number_to_letter' => $document->number_to_letter,
                        'link_xml' => $document->download_xml,
                        'link_pdf' => $document->download_pdf,
                        'link_cdr' => $document->download_cdr,
                    ]
                ]);
            } else {
                return to_json([
                    'success' => false,
                    'message' => $res_cdr['message'],
                ], 500);
            }
        } else {
            return to_json([
                'success' => false,
                'message' => $res['message'],
            ], 500);
        }
    }

    public function sendXml(Request $request)
    {
        $external_id = $request->input('external_id');
        $cpeBuilder = new CpeBuilder($external_id);
        $res = $cpeBuilder->sendXml();
        return to_json($res);
    }

    public function createPdf($builder)
    {
        $document = $builder->getDocument();
        $pdf = PDF::loadView('templates.pdf.default', compact('document') );
        return $pdf->output();
    }
}