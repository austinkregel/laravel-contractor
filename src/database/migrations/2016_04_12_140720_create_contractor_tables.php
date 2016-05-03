<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchTables extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        /*
         * jurisdictions have been moved to the users. Each user should have a required busines field.
         * Their Jurisdiciton requires them to only be able to view things from that business. no more
         * that.
         */

        Schema::create('contractor_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string ('name');
            $table->text('description'); // Describe the contracts
            // In desired intervals this date shall be the one referenced when
            // Looking to push notifications. So the dates will be calculated
            // 30, 60, and/or 90 days before this date.
            $table->text('who_its_through');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');

            $table->text('path');

            $table->integer('user_id')->unsigned();

            $table->uuid('uuid');
            $table->integer('old_contract')->unsigned();

            $table->softDeletes();
            $table->timestamps();
        });



        Schema::create('contractor_related_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('related_model_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->timestamps(); // For when it was assigned to the user.
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('dispatch_jurisdiction');
        Schema::drop('dispatch_jurisdiction_user');
        Schema::drop('dispatch_priority');
        Schema::drop('dispatch_ticket_user');
        Schema::drop('dispatch_tickets');
        Schema::drop('dispatch_ticket_edits');
        Schema::drop('dispatch_ticket_media');
        Schema::drop('dispatch_ticket_comments');
    }
}
