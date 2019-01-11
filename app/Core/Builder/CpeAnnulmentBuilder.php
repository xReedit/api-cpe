<?php

namespace App\Core\Builder;

use App\Core\Helpers\StorageAnnulment;
use App\Core\WS\Client\SunatEndpoints;
use App\Core\WS\Client\WSClient;
use App\Core\WS\Services\ExtService;
use App\Core\WS\Services\SummarySender;
use App\Models\Annulment;

class CpeAnnulmentBuilder
{
    protected $annulment;
    protected $client;
    protected $wsClient;
    protected $storageAnnulment;

    public function __construct($annulment_id)
    {
        $this->annulment = Annulment::with(['documents', 'user'])->find($annulment_id);
        $this->client = $this->annulment->user->client;

        $this->storageAnnulment = new StorageAnnulment();
        $this->storageAnnulment->annulmentById($this->annulment->id);

        $username = ($this->annulment->soap_type_id === '01')?'20000000000MODDATOS':$this->client->soap_username;
        $password = ($this->annulment->soap_type_id === '01')?'moddatos':$this->client->soap_password;

        $wsdl = ($this->annulment->soap_type_id === '01')?SunatEndpoints::FE_BETA:SunatEndpoints::FE_PRODUCCION;
        $this->wsClient = new WSClient();
        $this->wsClient->setCredentials($username, $password);
        $this->wsClient->setService($wsdl);
    }

    public function sendXml()
    {
        $xml_content = $this->storageAnnulment->getXml();

        $sender = new SummarySender();
        $result = $sender->send($this->wsClient, $this->annulment->filename, $xml_content);

        if ($result['success']) {
            $success = true;
            $ticket = $result['ticket'];
        } else {
            $success = false;
            $message = $result['error']['message'];
            $ticket = null;
        }

        return compact('success', 'message', 'state_type_id', 'ticket');
    }

    public function checkTicket()
    {
        $extService = new ExtService();
        $result = $extService->getStatus($this->wsClient, $this->annulment->ticket);

        if ($result['success']) {
            $this->storageAnnulment->uploadCdr($result['cdrXml']);
            $success = true;
            $message = $result['cdrResponse']['description'];
        } else {
            $success = false;
            $message = $result['error']['message'];
        }

        return compact('success', 'message');
    }
}