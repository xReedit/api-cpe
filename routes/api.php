<?php
 header('Access-Control-Allow-Origin: *');
 header('Access-Control-Allow-Methods: POST, GET');
 header('Access-Control-Allow-Headers: Content-Type, Authorization, application/json');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('services/ruc/{number}', 'Api\ServiceController@ruc');
    Route::get('services/dni/{number}', 'Api\ServiceController@dni');

//    Route::post('v20/invoice', 'Api\DocumentController@signedXml');
//    Route::post('v20/send_xml', 'Api\DocumentController@sendXml');
//    Route::post('v20/check_cdr', 'Api\DocumentController@checkCdr');

    // preparar documentos para resumen de boletas
    Route::post('summaries/getdocuments', 'SummaryController@getdocuments');
    // enviar resumen de boletas
    Route::post('summaries/resumen', 'SummaryController@store');

    // enviar de forma individual boelta o factura
    Route::post('send_xml', 'Api\DocumentController@sendXml');

    //
    Route::post('documents', 'Api\DocumentController@signedXml');

});

//Route::post('v2/sendXml', 'Api\DocumentController@sendXml');

Route::get('documents/download/{type}/{external_id}', 'DocumentController@download');