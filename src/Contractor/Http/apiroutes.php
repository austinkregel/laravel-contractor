<?php

Route::group(['prefix' => 'api/v1.0', 'as' => 'api.v1.', 'namespace' => 'Api', 'middleware' => 'auth'], function () {
    /**
     * Should return jpeg or pdf binary.
     */
    Route::get('document/{uuid}', ['as' => 'get-document', 'uses' => 'ApiController@displayContract']);
    Route::post('pdf-path/{id}', ['as' => 'new-pdf', 'uses' => 'ApiController@postContractCreate']);
    
    Route::delete('document/{uuid}', ['as' => 'delete', 'uses' => 'ApiAController@delete']);
});
