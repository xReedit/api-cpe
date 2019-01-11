<?php

namespace App\Core\Builder;

use App\Core\Helpers\StorageSummary;
use App\Core\WS\Client\SunatEndpoints;
use App\Core\WS\Client\WSClient;
use App\Core\WS\Services\ExtService;
use App\Core\WS\Services\SummarySender;
use App\Models\Summary;

class CpeSummaryBuilder
{
    protected $summary;
    protected $client;
    protected $wsClient;
    protected $storageSummary;

    public function __construct($summary_id)
    {
        $this->summary = Summary::with(['documents', 'user'])->find($summary_id);
        $this->client = $this->summary->user->client;

        $this->storageSummary = new StorageSummary();
        $this->storageSummary->summaryById($this->summary->id);

        $username = ($this->summary->soap_type_id === '01')?'20000000000MODDATOS':$this->client->soap_username;
        $password = ($this->summary->soap_type_id === '01')?'moddatos':$this->client->soap_password;

        $wsdl = ($this->summary->soap_type_id === '01')?SunatEndpoints::FE_BETA:SunatEndpoints::FE_PRODUCCION;
        $this->wsClient = new WSClient();
        $this->wsClient->setCredentials($username, $password);
        $this->wsClient->setService($wsdl);
    }

    public function sendXml()
    {
        $xml_content = $this->storageSummary->getXml();

        $sender = new SummarySender();
        $result = $sender->send($this->wsClient, $this->summary->filename, $xml_content);

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
        $result = $extService->getStatus($this->wsClient, $this->summary->ticket);

        if ($result['success']) {
            $this->storageSummary->uploadCdr($result['cdrXml']);
            $success = true;
            $message = $result['cdrResponse']['description'];
        } else {
            $success = false;
            $message = $result['error']['message'];
        }

        return compact('success', 'message');
    }
}