<?php
//Route::group(['prefix' => config('kregel.contractor.route'), 'as' => 'contractor::', 'middleware' => 'auth'], function () {
Route::group(['prefix' => config('kregel.contractor.route'), 'as' => 'contractor::', 'middleware' => config('kregel.contractor.middleware')], function () {

    /**
     * Api Routes for Dispatch.
     */
    require 'apiroutes.php';

    Route::get('/',                         ['as' => 'home', 'uses' => 'ContractsController@home']);
    Route::get('document/{place}',          ['as' => 'new',  'uses' => 'ContractsController@create']);
    Route::get('documents/{place}',         ['as' => 'list', 'uses' => 'ContractsController@showAContractor']);
    Route::get('documents/{place}/archive', ['as' => 'list.archived', 'uses' => 'ContractsController@showArchived']);
    Route::post('document/{place}',         ['as' => 'post', 'uses' => 'ContractsController@handlePost']);
    Route::get('document/{id}/edit',        ['as' => 'edit', 'uses' => 'ContractsController@edit']);
    Route::put('document/{place}',          ['as' => 'put', 'uses' => 'ContractsController@handlePut']);

    Route::delete('document/{id}/delete',   ['as' => 'delete', 'uses' => 'ContractsController@delete']);
});

