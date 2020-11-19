<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('password');
            $table->text('notes')->nullable();
            $table->string('token')->nullable();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->integer('customer_group_id')->unsigned()->nullable();
            $table->boolean('subscribed_to_news_letter')->default(0);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
