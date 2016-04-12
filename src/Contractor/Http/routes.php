<?php
//Route::group(['prefix' => config('kregel.contractor.route'), 'as' => 'contractor::', 'middleware' => 'auth'], function () {
Route::group(['prefix' => 'contractor', 'as' => 'contractor::', 'middleware' => 'auth'], function () {
    /**
     * Api Routes for Dispatch.
     */
    require 'apiroutes.php';

    Route::get('/', ['as' => 'home', 'uses' => 'ContractsController@home']);
    Route::get('document', ['as' => 'ticket', 'uses' => 'ContractsController@create']);
    Route::get('documents', ['as' => 'ticket', 'uses' => 'ContractsController@create']);
    Route::get('document/{uuid}', ['as' => 'media', 'uses' => 'MediaController@showMedia']);
    Route::get('document/{place?}/edit', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@getJurisdictionForEdit']);
});
