<?php

namespace App\Core\WS\Services;

use App\Core\Helpers\ErrorHelper;
use App\Core\Helpers\ZipHelper;
use App\Core\WS\Client\WSClient;

class SummarySender
{
    /**
     * @param WSClient $wsClient
     * @param string $filename
     * @param string $content
     *
     * @return array
     */
    public function send($client, $filename, $content)
    {
        $result = [];

        try {
            $zipContent = (new ZipHelper())->compress($filename.'.xml', $content);
            $params = [
                'fileName' => $filename.'.zip',
                'contentFile' => $zipContent,
            ];
            $response = $client->call('sendSummary', ['parameters' => $params]);
            $result['success'] = true;
            $result['ticket'] = $response->ticket;
        } catch (\SoapFault $e) {
            $result['success'] = false;
            $result['error'] =  ErrorHelper::getErrorFromFault($e);
        }

        return $result;
    }
}
