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
            // Used to server the contract
            $table->uuid('uuid');

            // In desired intervals this date shall be the one referenced when
            // Looking to push notifications. So the dates will be calculated
            // 30, 60, and/or 90 days before this date.
            $table->dateTime('notification_date');

            $sdf = "This is suppose to be place for NCG to upload their contracts and receive reminders of when they're do
            they need to be able to. ";

            $table->integer('notification_count');

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

        // This table will or at least should store info relating to the
        // media on the tickets.
        Schema::create('dispatch_ticket_media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->uuid('uuid');
            $table->string('type');
            // This is the full path of any given media
            $table->text('path');
            $table->timestamps();
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
