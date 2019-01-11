<?php

namespace App\Core\WS\Services;

use App\Core\Helpers\ErrorHelper;
use App\Core\Helpers\ZipHelper;
use App\Core\WS\Client\WSClient;
use App\Core\WS\Reader\CdrReader;

class BillSender
{
    /**
     * @param WSClient $wsClient
     * @param string $filename
     * @param string $content
     *
     * @return array
     */

    public function send($wsClient, $filename, $content)
    {
        $result = [];

        try {
            $zipContent = (new ZipHelper())->compress($filename.'.xml', $content);
            $params = [
                'fileName' => $filename.'.zip',
                'contentFile' => $zipContent,
            ];

            $response = $wsClient->call('sendBill', ['parameters' => $params]);
            $cdrZip = $response->applicationResponse;
            $cdrXml = (new ZipHelper())->decompressXmlFile($cdrZip);
            $result['success'] = true;
            $result['cdrXml'] = $cdrXml;
            $result['cdrResponse'] = (new CdrReader())->getCdrResponse($cdrXml);

        } catch (\SoapFault $e) {
            $result['success'] = false;
            $result['error'] = ErrorHelper::getErrorFromFault($e);
        }

        return $result;
    }
}
