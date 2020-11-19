<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFlatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_flat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('sku');
            $table->string('name')->nullable();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('url_key')->nullable();
            $table->boolean('new')->nullable();
            $table->boolean('featured')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('visible_individually')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 12, 4)->nullable();
            $table->decimal('min_price', 12, 4)->nullable();
            $table->decimal('max_price', 12, 4)->nullable();
            $table->decimal('cost', 12, 4)->nullable();
            $table->decimal('special_price', 12, 4)->nullable();
            $table->date('special_price_from')->nullable();
            $table->date('special_price_to')->nullable();
            $table->decimal('width', 12, 4)->nullable();
            $table->decimal('height', 12, 4)->nullable();
            $table->decimal('weight', 12, 4)->nullable();
            $table->decimal('depth', 12, 4)->nullable();
            $table->integer('color')->nullable();
            $table->string('color_label')->nullable();
            $table->integer('size')->nullable();
            $table->string('size_label');
            $table->datetime('created_at');
            $table->datetime('updated_at')->nullable();
            $table->string('locale')->nullable();
            $table->string('channel')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('product_id')->unsigned();
            $table->string('product_number')->nullable();

            $table->unique(['product_id', 'channel', 'locale'], 'product_flat_unique_index');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('product_flat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_flat');
    }
}
