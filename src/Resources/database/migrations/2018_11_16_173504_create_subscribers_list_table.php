<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersListTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('subscribers_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->integer('channel_id')->unsigned();
            $table->boolean('is_subscribed')->default(0);
            $table->string('token')->nullable();
            $table->timestamps();

            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('subscribers_list');
    }
}
