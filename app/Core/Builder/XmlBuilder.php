<?php

namespace App\Core\Builder;

use App\Core\Helpers\XmlHashHelper;
use App\Core\WS\Signed\SignedXml;
use App\Core\WS\Validator\SchemaValidator;
use App\Models\User;
use Milon\Barcode\DNS2D;

class XmlBuilder
{
    protected $document;
    protected $signedXML;
    protected $hash;

    public function createXMLSigned($builder)
    {
        $this->document = $builder->getDocument();
        $unsignedXML = $this->format_xml($this->viewXML($builder));
        $signer = new SignedXml();
        $signer->setCertificateFromFile($this->pathFilenameCertificate());
        $this->signedXML = $signer->signXml($unsignedXML);
        $res = $this->validate();
        $this->hash = $this->hash();

        if ($res['success']) {
            return [
                'success' => true,
                'document_id' => $this->document->id,
                'hash' => $this->hash,
                'xml' => $this->signedXML,
                'qr' => $this->qr()
            ];
        }
        return [
            'success' => false,
            'message' => $res['message'],
            'document_id' => $this->document->id,
            'xml' => $this->signedXML,
        ];
    }

    private function pathFilenameCertificate()
    {
        $user = User::with(['client'])->find(auth()->id());
        if ($this->document->soap_type_id === '01') {
            return app_path('Core'.DIRECTORY_SEPARATOR.'Certificates'.DIRECTORY_SEPARATOR.'demo.pem');
        }
        return storage_path('app'.DIRECTORY_SEPARATOR.$user->client->certificate);
    }

    private function viewXML($builder)
    {
        $classBuilder = get_class($builder);
        $view = null;
        switch (class_basename($classBuilder)) {
            case "InvoiceBuilder":
                $view = 'invoice';
                break;
            case "NoteCreditBuilder":
                $view = 'note_credit';
                break;
            case "NoteDebitBuilder":
                $view = 'note_debit';
                break;
            default:
                break;
        }
        return view('templates.xml.'.$this->document->ubl_version.'.'.$view, ['document' => $this->document])->render();
    }

    private function validate()
    {
        $ubl_version = ($this->document->ubl_version === 'v20')?'2.0':'2.1';
        $validator = new SchemaValidator();
        $validator->setVersion($ubl_version);

        if (!$validator->validate($this->signedXML)) {
            $success = false;
            $message = $validator->getMessage();
        } else {
            $success = true;
            $message = null;
        }
        return compact('success', 'message');
    }

    private function hash()
    {
        $helper = new XmlHashHelper();
        return $helper->getHashSign($this->signedXML);
    }

    private function qr()
    {
        $company = $this->document->company;
        $customer = $this->document->customer;
        $arr = join('|', [
            $company->number,
            $this->document->document_type_code,
            $this->document->series,
            $this->document->number,
            $this->document->total_igv,
            $this->document->total,
            $this->document->date_of_issue->format('Y-m-d'),
            $customer->identity_document_type_code,
            $customer->number,
            $this->hash

        ]);
        return DNS2D::getBarcodePNG($arr, "QRCODE", 3 , 3);
    }

    private function format_xml($xml, $formatOutput = TRUE, $declaration = TRUE)
    {
        $sxe = ($xml instanceof \SimpleXMLElement) ? $xml : simplexml_load_string($xml);
        $domElement = dom_import_simplexml($sxe);
        $domDocument = $domElement->ownerDocument;
        $domDocument->preserveWhiteSpace = false;
        $domDocument->formatOutput = (bool)$formatOutput;
        $domDocument->loadXML($sxe->asXML(), LIBXML_NOBLANKS);
        return (bool)$declaration ? $domDocument->saveXML() : $domDocument->saveXML($domDocument->documentElement);
    }
}