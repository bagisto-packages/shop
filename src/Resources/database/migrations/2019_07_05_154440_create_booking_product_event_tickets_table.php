<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingProductEventTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_product_event_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price', 12, 4)->default(0)->nullable();
            $table->integer('qty')->default(0)->nullable();
            $table->decimal('special_price', 12,4)->nullable();
            $table->dateTime('special_price_from')->nullable();
            $table->dateTime('special_price_to')->nullable();
            $table->integer('booking_product_id')->unsigned();

            $table->foreign('booking_product_id')->references('id')->on('booking_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_product_event_tickets');
    }
}
