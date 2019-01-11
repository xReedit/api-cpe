<?php

namespace App\Core\Builder;

use App\Core\Helpers\StorageDocument;
use App\Core\WS\Client\SunatEndpoints;
use App\Core\WS\Client\WSClient;
use App\Core\WS\Services\BillSender;
use App\Core\WS\Services\ExtService;
use App\Models\Document;

class CpeBuilder
{
    protected $document;
    protected $client;
    protected $wsClient;
    protected $storageDocument;

    public function __construct($external_id)
    {
        $this->document = Document::with(['user'])->where('external_id', $external_id)->first();
        $this->client = $this->document->user->client;

        $this->storageDocument = new StorageDocument();
        $this->storageDocument->documentById($this->document->id);

        $username = ($this->document->soap_type_id === '01')?'20000000000MODDATOS':$this->client->soap_username;
        $password = ($this->document->soap_type_id === '01')?'moddatos':$this->client->soap_password;

        $wsdl = ($this->document->soap_type_id === '01')?SunatEndpoints::FE_BETA:SunatEndpoints::FE_PRODUCCION;
        $this->wsClient = new WSClient();
        $this->wsClient->setCredentials($username, $password);
        $this->wsClient->setService($wsdl);
    }

    public function sendXml()
    {
        $xml_content = $this->storageDocument->getXml();

        $this->document->logs()->create(['message' => __('app.logs.document.send_xml')]);

        $sender = new BillSender();
        $result = $sender->send($this->wsClient, $this->document->filename, $xml_content);

        if ($result['success']) {
            $this->storageDocument->uploadCdr($result['cdrXml']);
            $success = true;
            $message = $result['cdrResponse']['description'];
            $state_type_id = '02';
        } else {
            $success = false;
            $message = $result['error']['message'];
            $state_type_id = '04';
        }

        $this->document->state_type_id = $state_type_id;
        $this->document->save();
        $this->document->logs()->create(['message' => $message]);

        return compact('success', 'message', 'state_type_id');
    }

    public function checkCdrStatus()
    {
//        $this->wsClient->setService(SunatEndpoints::);
        $extService = new ExtService();
        $result = $extService->getCdrStatus($this->wsClient,
                                            $this->document->company->number,
                                            $this->document->document_type_code,
                                            $this->document->series,
                                            $this->document->number);

        if ($result['success']) {
            $this->storageDocument->uploadCdr($result['cdrXml']);
            $success = true;
            $message = $result['cdrResponse']['description'];
            $state_type_id = '02';
        } else {
            $success = false;
            $message = $result['error']['message'];
            $state_type_id = '04';
        }

        $this->document->state_type_id = $state_type_id;
        $this->document->save();
        $this->document->logs()->create(['message' => $message]);

        return compact('success', 'message', 'state_type_id');
    }
}