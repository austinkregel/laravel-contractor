<?php
//Route::group(['prefix' => config('kregel.contractor.route'), 'as' => 'contractor::', 'middleware' => 'auth'], function () {
Route::group(['prefix' => config('kregel.contractor.route'), 'as' => 'contractor::', 'middleware' => 'auth'], function () {

    /**
     * Api Routes for Dispatch.
     */
    require 'apiroutes.php';

    Route::get('test', function(){
        echo 'hello';
        $contract = \Kregel\Contractor\Models\Contract::findOrNew(1);
        foreach($contract->paths as $path){
            echo $path->path. "\n";
        }
    });

    Route::get('/',                         ['as' => 'home', 'uses' => 'ContractsController@home']);
    Route::get('document/{place}',          ['as' => 'new',  'uses' => 'ContractsController@create']);
    Route::get('documents/{place}',         ['as' => 'list', 'uses' => 'ContractsController@showAContractor']);
    Route::post('document/{place}',         ['as' => 'post', 'uses' => 'ContractsController@handlePost']);
    Route::get('document/{id}/edit',        ['as' => 'edit', 'uses' => 'ContractsController@edit']);
    Route::put('document/{place}',          ['as' => 'put', 'uses' => 'ContractsController@handlePut']);

});

