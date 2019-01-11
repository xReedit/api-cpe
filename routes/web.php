<?php

Route::auth();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'DashboardController@index');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');

    Route::get('clients', 'ClientController@index')->name('clients.index');
    Route::get('clients/records', 'ClientController@records');
    Route::get('clients/tables', 'ClientController@tables');
    Route::get('clients/create/{client}', 'ClientController@create');
    Route::post('clients', 'ClientController@store');
    Route::delete('clients/{client}', 'ClientController@destroy');
    Route::post('clients/uploads', 'ClientController@uploadFile');

    Route::get('documents', 'DocumentController@index')->name('documents.index');
    Route::get('documents/records', 'DocumentController@records');
    Route::get('documents/download/{type}/{external_id}', 'DocumentController@download')->name('documents.download');

    Route::get('summaries', 'SummaryController@index')->name('summaries.index');
    Route::get('summaries/records', 'SummaryController@records');
    Route::get('summaries/tables', 'SummaryController@tables');
    Route::post('summaries/search_documents', 'SummaryController@searchDocuments');
    Route::post('summaries', 'SummaryController@store');
    Route::get('summaries/check_ticket/{summary}', 'SummaryController@checkTicket');
    Route::get('summaries/download/{type}/{id}', 'SummaryController@download')->name('summaries.download');

    Route::get('summaries_annulment', 'SummaryAnnulmentController@index')->name('summaries_annulment.index');
    Route::get('summaries_annulment/records', 'SummaryAnnulmentController@records');
    Route::get('summaries_annulment/tables', 'SummaryAnnulmentController@tables');
    Route::post('summaries_annulment/search_documents', 'SummaryAnnulmentController@searchDocuments');
    Route::post('summaries_annulment', 'SummaryAnnulmentController@store');
    Route::get('summaries_annulment/check_ticket/{summary}', 'SummaryAnnulmentController@checkTicket');

    Route::get('annulments', 'AnnulmentController@index')->name('annulments.index');
    Route::get('annulments/records', 'AnnulmentController@records');
    Route::get('annulments/tables', 'AnnulmentController@tables');
    Route::post('annulments/search_documents', 'AnnulmentController@searchDocuments');
    Route::post('annulments', 'AnnulmentController@store');
    Route::get('annulments/check_ticket/{summary}', 'AnnulmentController@checkTicket');
    Route::get('annulments/download/{type}/{id}', 'AnnulmentController@download')->name('annulments.download');

    Route::post('send_xml', 'Api\DocumentController@sendXml');
//    Route::post('clients/uploads', 'ClientController@uploadFile');
//    Route::get('documents/printer/{document}', 'DocumentController@printer');
//    Route::get('documents/annul/{document}', 'DocumentController@annul');
//    Route::post('documents/send', 'DocumentController@send');
});
