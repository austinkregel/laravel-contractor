<?php

Route::group(['prefix' => 'api/v1.0', 'as' => 'api.v1.'], function () {
    Route::group(['as' => 'new.'],function(){
        Route::get('documents', ['as' => 'document', 'uses' => 'TicketsController@postTicketCreate']);
        /**
         * Should return jpeg or pdf binary.
         */
        Route::get('document/{uuid}', ['as' => 'document', 'uses' => 'TicketsController@postTicketCreate']);

        Route::post('document', ['as' => 'document', 'uses' => 'TicketsController@postTicketCreate']);
    });
});
