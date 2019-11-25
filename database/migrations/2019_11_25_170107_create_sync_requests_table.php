<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('initiator_id');
            $table->foreign('initiator_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('acceptor_id');
            $table->foreign('acceptor_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['initiator_id', 'acceptor_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_requests');
    }
}
