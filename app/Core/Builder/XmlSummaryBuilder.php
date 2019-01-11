<?php

namespace App\Core\Builder;

use App\Core\WS\Signed\SignedXml;
use App\Core\WS\Validator\SchemaValidator;
use App\Models\User;

class XmlSummaryBuilder
{
    protected $summary;
    protected $signedXML;
    protected $hash;

    public function createXMLSigned($builder)
    {
        $this->summary = $builder->getSummary();
        $unsignedXML = $this->format_xml($this->viewXML($builder));
        $signer = new SignedXml();
        $signer->setCertificateFromFile($this->pathFilenameCertificate());
        $this->signedXML = $signer->signXml($unsignedXML);
        $res = $this->validate();

        if ($res['success']) {
            return [
                'success' => true,
                'summary_id' => $this->summary->id,
                'xml' => $this->signedXML,
            ];
        }
        return [
            'success' => false,
            'message' => $res['message'],
            'xml' => $this->signedXML,
        ];
    }

    private function pathFilenameCertificate()
    {
        $user = User::with(['client'])->find(1);
        if ($this->summary->soap_type_id === '01') {
            return app_path('Core'.DIRECTORY_SEPARATOR.'Certificates'.DIRECTORY_SEPARATOR.'demo.pem');
        }
        return storage_path('app'.DIRECTORY_SEPARATOR.$user->client->certificate);
    }

    private function viewXML($builder)
    {
        $classBuilder = get_class($builder);
        $view = null;
        switch (class_basename($classBuilder)) {
            case "SummaryBuilder":
                $view = 'summary';
                break;
            default:
                break;
        }
        return view('templates.xml.'.$view, ['summary' => $this->summary])->render();
    }

    private function validate()
    {
        $ubl_version = '2.0';
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